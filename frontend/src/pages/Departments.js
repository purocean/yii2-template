import React from 'react';
import { Card, Button, Row, Col, message } from 'antd';
import Http from '../utils/Http';

import Main from '../layouts/Main';
import Crud from '../components/Crud';

import CrudActions from '../actions/CrudActions';
import CrudStore from '../stores/CrudStore';

class Component extends React.Component {
  constructor(props){
    super(props);
    this.onCrudChange = this.onCrudChange.bind(this);

    this.state = {
      status: 'ok', // ok | syncing | sending
      sendMsg: {},
      crud: CrudStore.getState(),
    };

    this.resourceName = 'departments';
    this.saveForm = null;
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

  componentDidMount() {
    CrudStore.listen(this.onCrudChange);
    CrudActions.setResourceName(this.resourceName);
    CrudActions.fetch();
  }

  componentWillUnmount() {
    CrudStore.unlisten(this.onCrudChange);
  }

  onCrudChange(state) {
    this.setState({crud: state});
  }

  sync() {
    this.setState({status: 'syncing'});
    Http.fetch(
      '/departments/sync',
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

  render() {
    const panel = (
      <Row style={{marginBottom: '1em'}}>
        <Col span={24}>
          <Button
            type="primary"
            onClick={() => this.sync()}
            loading={this.state.status === 'syncing'}
          >从企业号同步</Button>
        </Col>
      </Row>
    );

    return (
      <Main className="departments" navKey="/departments" sideBar={false}>
        <Card>
          <Crud
            resourceName={this.resourceName}
            panel={panel}
            rowSelection={null}
            tableColumns={this.columns}>
          </Crud>
        </Card>
      </Main>
    );
  }
}

Component.defaultProps = {
};

export default Component;
