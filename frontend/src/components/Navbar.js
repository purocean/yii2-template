import '../styles/Navbar.css';

import Config from 'config';
import React from 'react';
import { Link } from 'react-router';
import { Row, Col } from 'antd';

class Component extends React.Component {
  render() {
    return (
        <Row className="navbar">
          <Col span={4}>
            <Link className="brand pull-right" to="/">{this.props.brand}</Link>
          </Col>
          <Col span={16}>
            {this.props.children}
          </Col>
          <Col span={4}></Col>
        </Row>
    );
  }
}

Component.defaultProps = {
  brand: Config.siteName
};


export default Component;
