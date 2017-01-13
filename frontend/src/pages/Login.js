import '../styles/Login.css';

import React from 'react';
import {Menu, Icon, Form, Input, Checkbox, Card, Button, message} from 'antd';
import Config from 'config';
import QRCode from 'jr-qrcode';
import SocketLookup from 'socket.io-client';

import Auth from '../auth/Auth';
import Http from '../utils/Http';

import Navbar from '../components/Navbar';

class Component extends React.Component {
  static contextTypes = {
    router: React.PropTypes.object.isRequired
  };

  constructor(props) {
    super(props);

    this.state = {
      isSubmit: false,
      isQrcode: true,
      nonce: '',
      qrUrl: '',
      error: '请扫码',
    };

    Auth.setUser('');
    Auth.setPermissions({});
    Auth.setRoles({});
  }

  componentDidMount () {
    Http.fetch('/user/qrlogin', {}, data => {
      this.setState({
        qrUrl: data.data.url,
        nonce: data.data.nonce,
      });

      const socketIOPort = Config.socketIOPort
      if (socketIOPort) { // 如果配置了 socket port 就用 websocket 通信
        const io = SocketLookup(`//${location.hostname}:${socketIOPort}`);

        io.on('connect', () => {
          io.emit('qrlogin', data.data.nonce);
        });

        io.on('qrlogin', data => {
          if (data.status === 'ok') {
            this.success(data.data);
            io.close();
          } else {
            this.setState({error: data.errors.nonce.toString()})
          }
        });
      } else {
        let timer = setInterval(() => {
          if (this.state.isQrcode) {
            Http.fetch(
              '/user/qrlogin' + '?nonce=' + encodeURIComponent(this.state.nonce),
              { method: 'post'},
              data => {
                if (data.status === 'ok') {
                  this.success(data.data);
                  window.clearInterval(timer);
                } else {
                  this.setState({error: data.errors.nonce.toString()})
                }
              }
            );
          }
        }, 3000);
      }
    });
  }

  success(user) {
    Auth.setUser(user);
    let next = this.props.location.query.redirect;
    this.context.router.replace(next ? next : '/');
  }

  switchLogin() {
    this.setState({'isQrcode': !this.state.isQrcode});
  }

  onSubmit(e) {
    e.preventDefault();
    this.setState({isSubmit: true});

    Http.fetch('/user/login', {
      method: 'post',
      body: this.props.form.getFieldsValue()
    }, data => {
        if (data.status === 'ok') {
          Auth.setUser(data.data);
          let next = this.props.location.query.redirect;
          this.context.router.replace(next ? next : '/');
        } else {
          this.props.form.setFieldsValue({password: ''});
          message.error(data.errors.password.toString());
          console.log(data);
        }
    })
    .then(() => this.setState({isSubmit: false}));
  }

  render() {
    const { getFieldDecorator } = this.props.form;
    return (
      <div className="login">
        <Navbar>
          <Menu  mode="horizontal" theme="dark" defaultSelectedKeys={['login']}>
            <Menu.Item className="pull-right" key="login">
              <Icon type="user" />登录
            </Menu.Item>
          </Menu>
        </Navbar>
        <Card className="main">
          <h1 className="title">登录</h1>
          <a onClick={() => this.switchLogin()} className="switch">{this.state.isQrcode ? '账号密码' : '二维码'}登录</a>
          <Form className={this.state.isQrcode ? 'hidden' : ''} vertical onSubmit={e => this.onSubmit(e)}>
            <Form.Item label="用户名">
              {getFieldDecorator('username', {
                rules: [
                  { required: true, whitespace: true, message: '请输入用户名' }
                ]
              })(
                <Input type="text" required />
              )}
            </Form.Item>
            <Form.Item label="密码">
              {getFieldDecorator('password', {
                rules: [
                  { required: true, whitespace: true, message: '请输入密码' }
                ]
              })(
                <Input type="password" autoComplete="off" required />
              )}
            </Form.Item>
            <Form.Item>
              {getFieldDecorator('rememberMe', {})(
                <Checkbox>记住我</Checkbox>
              )}
              <Button loading={this.state.isSubmit} className="pull-right" type="primary" size="large" htmlType="submit">登录</Button>
            </Form.Item>
          </Form>
          <div  className={this.state.isQrcode && this.state.qrUrl ? 'qrlogin' : 'qrlogin hidden'}>
            <h4>{this.state.error}</h4>
            <img src={QRCode.getQrBase64(this.state.qrUrl)} />
          </div>
        </Card>
      </div>
    );
  }
}

Component.defaultProps = {
};

Component = Form.create()(Component);

export default Component;
