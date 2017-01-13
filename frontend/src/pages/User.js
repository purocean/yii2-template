import React from 'react';
import { Card, Button, Row, Col, Modal, Form, Input, Tooltip, message, Checkbox } from 'antd';
import Http from '../utils/Http';

import Main from '../layouts/Main';
import SearchInput from '../components/SearchInput';
import Crud from '../components/Crud';

import CrudActions from '../actions/CrudActions';
import CrudStore from '../stores/CrudStore';

const FormItem = Form.Item;

class SaveForm extends React.Component {
  constructor(props){
    super(props);
  }

  render() {
    const { getFieldDecorator } = this.props.form;
    return (
      <Form horizontal>
        <Row>
          <Col span={24}>
            <FormItem>
              {getFieldDecorator('username', {
                initialValue: this.props.initialValues.username
              })(<Input type="hidden" />)}

              {getFieldDecorator('roles', {
                initialValue: this.props.initialValues.roles
              })(
                <Checkbox.Group options={Object.keys(this.props.stuff.roles).map(key => {
                  return {label: this.props.stuff.roles[key], value: key}
                })} />
              )}
            </FormItem>
          </Col>
        </Row>
      </Form>
    );
  }
}

SaveForm = Form.create()(SaveForm);

class Component extends React.Component {
  constructor(props){
    super(props);
    this.onCrudChange = this.onCrudChange.bind(this);

    this.state = {
      status: 'ok', // ok | syncing | sending
      sendMsg: {},
      crud: CrudStore.getState(),
    };

    this.resourceName = 'user';
    this.saveForm = null;
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
                <Button type="ghost" shape="circle-outline" icon="user" onClick={() => {
                  this.saveForm && this.saveForm.resetFields(); // this.saveForm 有可能为 null，如果form
                  CrudActions.showSaveForm(`给 ${record.name} 分配角色`, record);
                }}/>
              </Tooltip>
              <Tooltip placement="top" title="发送消息">
                <Button type="ghost" shape="circle-outline" icon="message" onClick={() => {
                  this.showMsgBox(record.username, record.name, '');
                }}/>
              </Tooltip>
            </div>
          );
        },
      }
    ];
  }

  componentDidMount() {
    CrudStore.listen(this.onCrudChange);
    CrudActions.setResourceName(this.resourceName);
    CrudActions.fetchStuff();
    CrudActions.fetch();
  }

  componentWillUnmount() {
    CrudStore.unlisten(this.onCrudChange);
  }

  onCrudChange(state) {
    this.setState({crud: state});
  }

  onSearch(value) {
    CrudActions.fetch({page: 1, key: value});
  }

  save() {
    this.saveForm.validateFieldsAndScroll(errors => {
      errors === null && CrudActions.save(this.saveForm.getFieldsValue())
    });
  }

  sync() {
    this.setState({status: 'syncing'});
    Http.fetch(
      '/user/sync',
      {method: 'post'},
      data => {
        if (data.status === 'ok') {
          message.success(data.message);
        } else {
          message.error(data.message);
        }

        this.setState({status: 'ok'});
        CrudActions.fetch();
      }
    );
  }

  showMsgBox(username, name, msg) {
    this.setState({sendMsg: {username, name: name ? name : '', msg: msg ? msg : ''}})
  }

  sendMessage() {
    this.setState({status: 'sending'});
    Http.fetch(
      '/user/sendmsg',
      {
        method: 'post',
        body: {
          username: this.state.sendMsg.username,
          message: this.state.sendMsg.msg,
        }
      },
      data => {
        if (data.status === 'ok') {
          message.success(data.message);
        } else {
          message.error(data.message);
        }

        this.showMsgBox();
        this.setState({status: 'ok'});
      }
    );
  }

  render() {
    const panel = (
      <Row style={{marginBottom: '1em'}}>
        <Col span={6}>
          <Button
            type="primary"
            onClick={() => this.sync()}
            loading={this.state.status === 'syncing'}
          >从企业号同步</Button>
        </Col>
        <Col span={18}>
          <SearchInput placeholder="搜索姓名/电话/部门..." value={this.state.searchKey} onSearch={value => this.onSearch(value)} style={{ width: 300 ,float:'right'}}/>
        </Col>
      </Row>
    );

    const saveModal = (
      <Modal
        title={this.state.crud.saveModal.title}
        visible={this.state.crud.saveModal.visible}
        confirmLoading={this.state.crud.status === 'saving'}
        okText="保存"
        onOk={() => this.save()}
        onCancel={CrudActions.hideSaveForm}>
        <SaveForm
          ref={form => this.saveForm = form}
          stuff={this.state.crud.stuff}
          initialValues={this.state.crud.saveModal.initialValues} />
      </Modal>
    );

    return (
      <Main className="user" navKey="/user" sideBar={false}>
        <Card>
          <Crud
            resourceName={this.resourceName}
            panel={panel}
            tableColumns={this.columns}
            rowSelection={null}
            saveModal={saveModal}>
          </Crud>
        </Card>

        <Modal
          title={`给 ${this.state.sendMsg.name} 发消息`}
          visible={!!this.state.sendMsg.username}
          onOk={() => this.sendMessage()}
          onCancel={() => this.showMsgBox()}
          confirmLoading={this.state.status === 'sending'}>
          <Input
            type="textarea"
            value={this.state.sendMsg.msg}
            onChange={(e) => this.showMsgBox(this.state.sendMsg.username, this.state.sendMsg.name, e.target.value)} />
        </Modal>
      </Main>
    );
  }
}

Component.defaultProps = {
};

export default Component;
