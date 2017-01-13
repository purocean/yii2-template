import React from 'react';
import { Router, Route, hashHistory } from 'react-router';

import Auth from '../auth/Auth';
import Wx from '../utils/Wx';

import Login from '../mobile/Login';
import Qrlogin from '../mobile/Qrlogin';
import Err from '../pages/Err';

const requireAuth = (prevState, nextState, replace, callback) => {
  Wx.hideOptionMenu(); // 隐藏右上角菜单
  Auth.requireAuth(prevState, nextState, replace, callback);
};

class AppRouter extends React.Component {
  render() {
    return (
      <div>
        <Router history={hashHistory}>
          <Route
            path="/"
            onChange={requireAuth}
            onEnter={(nextState, replace, callback) => requireAuth(null, nextState, replace, callback)}
          >
            <Route path="login(/:code)" component={Login} />
            <Route path="qrlogin/:nonce" component={Qrlogin} />
          </Route>
          <Route path="/error" component={Err} />
        </Router>
      </div>
    );
  }
}

AppRouter.defaultProps = {
};

export default AppRouter;
