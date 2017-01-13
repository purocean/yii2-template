import React from 'react';
import { Toast } from 'antd-mobile';

import Auth from '../auth/Auth';
import Http from '../utils/Http';

class Component extends React.Component {
  static contextTypes = {
    router: React.PropTypes.object.isRequired
  };

  constructor(props) {
    super(props);

    Auth.setUser('');
    Auth.setPermissions({});
    Auth.setRoles({});
  }

  componentDidMount () {
    const next = this.props.location.query.redirect;

    if (!this.props.params.code) {
      Toast.loading('获取微信授权……', 0);
      location.href = Http.getUrl('/app/auth?redirect=' + encodeURIComponent(next));

      return;
    }

    Toast.loading('登录中……', 0);
    Http.fetch(
      '/user/codelogin' + '?code=' + encodeURIComponent(this.props.params.code),
      {method: 'post'},
      data => {
        Toast.hide();
        if (data.status === 'ok') {
          Auth.setUser(data.data);
          this.context.router.replace(next ? next : '/');
        } else {
          Toast.fail(data.errors.code.toString(), 0);
          console.log(data);
        }
      }
    );
  }

  render() {
    return (
      <div>
      </div>
    );
  }
}

Component.defaultProps = {
  code: '',
};

export default Component;
