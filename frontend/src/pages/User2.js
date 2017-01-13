import React from 'react';
import { Card, Row ,Col ,Button, Table, Modal, Input, Checkbox, Tooltip } from 'antd';

import '../styles/User.css';
import Main from '../layouts/Main';
import SearchInput from '../components/SearchInput';

import UserActions from '../actions/UserActions';
import UserStore from '../stores/UserStore';

const CheckboxGroup = Checkbox.Group;

class Component extends React.Component {
  constructor (props) {
    super(props);

    this.state = UserStore.getState();
    this.onChange = this.onChange.bind(this);
    this.columns = [
      {
        title: 'Id',
        dataIndex: 'id',
        width: '4em',
      },
      {
        title: '用户名',
        dataIndex: 'username',
      },
      {
        title: '姓名',
        dataIndex: 'name',
      },
      {
        title: '邮件',
        dataIndex: 'email',
      },
      {
        title: '手机号',
        dataIndex: 'mobile',
      },
      {
        title: '部门',
        dataIndex: 'department_name',
      },
      {
        title: '更新时间',
        width: '16em',
        dataIndex: 'created_at',
        render: timeStamp => new Date(timeStamp * 1000).toLocaleString(),
      },
      {
        title: '操作',
        width: '6em',
        render: (text, record) => {
          return (
            <div style={{width: '56px'}}>
              <Tooltip placement="top" title="编辑用户">
                <Button type="ghost" shape="circle-outline" icon="user" onClick={() => UserActions.limiteBox(record.username, record.name, record.roles, record.zone, record.isp, record.design_institute)}/>
              </Tooltip>
              <Tooltip placement="top" title="发送消息">
                <Button type="ghost" shape="circle-outline" icon="message" onClick={() => UserActions.msgBox(record.username, record.name, '')}/>
              </Tooltip>
            </div>
          );
        },
      }
    ];
  }

  componentDidMount () {
    UserStore.listen(this.onChange);
    UserActions.fetch();
    UserActions.fetchRoles();
  }

  componentWillUnmount () {
    UserStore.unlisten(this.onChange);
  }

  onChange (state) {
    this.setState(state);
  }

  handleTableChange (pagination, filters, sorter) {
    UserActions.fetch({
      key: this.state.searchKey,
      page: pagination.current,
      sortField: sorter.field,
      sortOrder: sorter.order,
      ...filters
    });
  }

  onSearch(value) {
    UserActions.fetch({
      page: 1,
      key: value,
    });
  }

  render () {
    return (
      <Main className="user" navKey="/user" sideBar={false}>
        <Card>
          <Row style={{marginBottom: '1em'}}>
            <Col span={6}>
              <Button
                type="primary"
                onClick={UserActions.sync}
                loading={this.state.status === 'syncing'}
              >从企业号同步</Button>
            </Col>
            <Col span={18}>
              <SearchInput placeholder="搜索姓名/电话/部门..." value={this.state.searchKey} onSearch={value => this.onSearch(value)} style={{ width: 300 ,float:'right'}}/>
            </Col>
          </Row>

          <Table
            bordered
            size="middle"
            columns={this.columns}
            rowKey={record => record.id}
            dataSource={this.state.list}
            pagination={this.state.pagination}
            loading={this.state.status === 'loading'}
            onChange={(pagination, filters, sorter) => this.handleTableChange(pagination, filters, sorter)}
          />
        </Card>

        <Modal
          title={`给 ${this.state.sendMsg.name} 发消息`}
          visible={!!this.state.sendMsg.username}
          onOk={UserActions.sendMessage}
          onCancel={() => UserActions.msgBox('', '', '')}
          confirmLoading={this.state.status === 'sending'}>
          <Input
            type="textarea"
            value={this.state.sendMsg.message}
            onChange={(e) => UserActions.msgBox(this.state.sendMsg.username, this.state.sendMsg.name, e.target.value)} />
        </Modal>

        <Modal
          title={`给 ${this.state.sendLimite.name} 分配权限`}
          visible={!!this.state.sendLimite.username}
          onOk={UserActions.sendRoles}
          onCancel={() => UserActions.limiteBox('', '', [], '', '')}
          confirmLoading={this.state.status === 'assigning'} >
          <div className="rollbox">
            <CheckboxGroup options={this.state.allRoles} value={this.state.sendLimite.userlimite}
              onChange={(e) => UserActions.limiteBox(this.state.sendLimite.username, this.state.sendLimite.name, e)} />
          </div>
        </Modal>
      </Main>
    );
  }
}

Component.defaultProps = {
};

export default Component;
