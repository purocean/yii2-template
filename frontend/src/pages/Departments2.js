import React from 'react';
import Main from '../layouts/Main';
import { Card, Button, Table } from 'antd';

import DepartmentsActions from '../actions/DepartmentsActions';
import DepartmentsStore from '../stores/DepartmentsStore';

class Component extends React.Component {
  constructor(props){
    super(props);

    this.state = DepartmentsStore.getState();
    this.onChange = this.onChange.bind(this);

    this.columns = [
      {
        title: '序号',
        dataIndex: 'id',
        width: '4em',
      },
      {
        title: '名称',
        dataIndex: 'name',
      },
      {
        title: '更新时间',
        width: '16em',
        dataIndex: 'created_at',
        render: timeStamp => new Date(timeStamp * 1000).toLocaleString(),
      },
    ];
  }

  componentDidMount () {
    DepartmentsStore.listen(this.onChange);
    DepartmentsActions.fetch();
  }

  componentWillUnmount () {
    DepartmentsStore.unlisten(this.onChange);
  }

  onChange (state) {
    this.setState(state);
  }

  handleTableChange (pagination, filters, sorter) {
    DepartmentsActions.fetch({
      key: this.state.searchKey,
      page: pagination.current,
      sortField: sorter.field,
      sortOrder: sorter.order,
      ...filters
    });
  }

  render() {
    return (
      <Main className="departments" navKey="/departments" sideBar={false}>
        <Card>
          <Button
            type="primary" style={{marginBottom:'1em'}}
            onClick={DepartmentsActions.sync}
            loading={this.state.status === 'syncing'}
            >单位/部门同步</Button>
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
      </Main>
    );
  }
}

Component.defaultProps = {
};

export default Component;
