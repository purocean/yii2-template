import React from 'react';
import { Table } from 'antd';

import CrudActions from '../actions/CrudActions';
import CrudStore from '../stores/CrudStore';

class Component extends React.Component {
  constructor(props){
    super(props);
    this.onChange = this.onChange.bind(this);

    this.state = CrudStore.getState();

    this.rowSelection = {
      onChange: (selectedRowKeys, selectedRows) => {
        CrudActions.rowSelectionChange(selectedRowKeys, selectedRows);
      },
      onSelect: (record, selected, selectedRows) => {
        CrudActions.rowSelectionSelect(record, selected, selectedRows);
      },
      onSelectAll: (selected, selectedRows, changeRows) => {
        CrudActions.rowSelectionSelectAll(selected, selectedRows, changeRows);
      },
    };
  }

  componentDidMount() {
    CrudStore.listen(this.onChange);
  }

  componentWillUnmount() {
    CrudStore.unlisten(this.onChange);
  }

  onChange(state) {
    this.setState(state);
  }

  handleTableChange(pagination, filters, sorter) {
    CrudActions.fetch(false, {
      page: pagination.current,
      sortField: sorter.field,
      sortOrder: sorter.order,
      ...filters
    });
  }

  render() {
    return (
        <div>
          {this.props.panel}
          {this.props.saveModal}
          <Table
            bordered
            rowKey={record => record.id}
            size={this.props.tableSize}
            rowSelection={typeof this.props.rowSelection === 'undefined' ? this.rowSelection : this.props.rowSelection}
            columns={this.props.tableColumns}
            dataSource={this.state.list}
            loading={this.state.status === 'fetching'}
            pagination={this.state.pagination}
            onChange={(pagination, filters, sorter) => this.handleTableChange(pagination, filters, sorter)} />
        </div>
    );
  }
}

Component.defaultProps = {
  tableSize: 'middle',
};

export default Component;
