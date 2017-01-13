import '../styles/Main.css';

import React from 'react';
import { Link } from 'react-router';

import { Menu, Icon } from 'antd';

import Auth from '../auth/Auth';
import Navbar from '../components/Navbar';
import Http from '../utils/Http';

class Component extends React.Component {
  static contextTypes = {
    router: React.PropTypes.object.isRequired
  };

  constructor(props){
    super(props);

    this.state = {
      mainNav: {
        '/user': '用户管理',
        '/departments': '单位/部门管理',
      }
    };
  }

  handleMenuClick(e) {
    if (e.key === 'logout') {
      Http.fetch('/user/logout', {method: 'POST'})
      .then(() => {
        this.context.router.push('/login');
      })
    }
  }

  render () {
    return (
      <div className={'layout-main ' + this.props.className}>
        <Navbar>
          <Menu mode="horizontal" theme="dark" onClick={e => this.handleMenuClick(e)} defaultSelectedKeys={[this.props.navKey]}>
            {Object.keys(this.state.mainNav).filter(key => {
              return Auth.can(key);
            }).map(key => {
              return (<Menu.Item key={key}><Link to={key}> {this.state.mainNav[key]} </Link></Menu.Item>);
            })}
            {this.props.extNav.map(nav => {
              return (<Menu.Item key={nav.key}>{nav.to ? (<Link to={nav.key}> {nav.name} </Link>) : nav.name} </Menu.Item>)
            })}
            <Menu.SubMenu className="pull-right" title={
              <span>
                <Icon type="user" />
                {Object.values(Auth.getRoles()).toString()} {Auth.getUser().name ? Auth.getUser().name : Auth.getUser().username}
              </span>
            }>
              <Menu.Item key="logout">退出登录</Menu.Item>
            </Menu.SubMenu>
          </Menu>
        </Navbar>
        <div className="main">
          {this.props.children}
        </div>
      </div>
    );
  }
}

Component.defaultProps = {
  className: '',
  navKey: '/',
  sideBar: false,
  extNav: [],
};

export default Component;
