import React from 'react';
import { Toast, Result } from 'antd-mobile';

import Auth from '../auth/Auth';
import Http from '../utils/Http';

import ImgInfo from '../images/info.png';

class Component extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      show: false,
    }
  }

  componentDidMount () {
    Toast.loading('获取数据……', 0);
    Http.fetch(
      '/user/confirmlogin' + '?nonce=' + encodeURIComponent(this.props.params.nonce),
      { method: 'post'},
      () => {
        Toast.hide();
        this.setState({show: true});
      }
    );
  }

  login() {
    Toast.loading('确认登录……', 0);
    Http.fetch(
      '/user/confirmlogin' + '?nonce=' + encodeURIComponent(this.props.params.nonce) + '&allow=1',
      { method: 'post'},
      data => {
        Toast.hide();
        console.log(data);
        if (data.status === 'ok') {
          Toast.success('登录成功');
          window.WeixinJSBridge.invoke('closeWindow');
        } else {
          Toast.fail(data.errors.code.toString(), 0);
          console.log(data);
        }
      }
    );
  }

  render() {
    return (
      <div style={{paddingTop: '6em', display: this.state.show ? 'block' : 'none'}}>
        <Result
          imgUrl={ImgInfo}
          title="扫码登录"
          message={Auth.getUser().name}
          buttonText="确认登录"
          buttonType="primary"
          buttonClick={() => this.login()}
        />
      </div>
    );
  }
}

Component.defaultProps = {
  code: '',
};

export default Component;
