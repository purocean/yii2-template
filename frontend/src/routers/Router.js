import React from 'react';
import { Router, Route, IndexRedirect, hashHistory } from 'react-router';

import Auth from '../auth/Auth';

import Err from '../pages/Err';
import User from '../pages/User';
import Login from '../pages/Login';
import Departments from '../pages/Departments';

class AppRouter extends React.Component {
  render() {
    return (
      <div>
        <Router history={hashHistory}>
          <Route
            path="/"
            onChange={Auth.requireAuth}
            onEnter={(nextState, replace, callback) => Auth.requireAuth(null, nextState, replace, callback)}
          >
            <IndexRedirect to="user" />
            <Route path="login" component={Login} />
            <Route path="user" component={User} />
            <Route path="departments" component={Departments} />
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
