import React from 'react';
import '../styles/Error.css';
import Cry from '../images/cry.png';
import Font from '../images/kaixiaochai.png';

class Component extends React.Component {
  static contextTypes = {
    router: React.PropTypes.object.isRequired
  };

  constructor(props){
    super(props);
  }

  render() {
    return (
      <div className="err">
        <div className="box">
          <div className="container">
            <div className="containerbox">
              <div className="img"><img className="png" src={Cry}/></div>
              <div className="fontbox">
                <div className="font"><img className="png" src={Font}/></div>
                <div className="fontstyle">
                  {this.props.location.query.code ? <b>{this.props.location.query.code}</b> : null}
                  {this.props.location.query.detail ? <div>{this.props.location.query.detail}</div> : null}
                </div>
                <div className="back" onClick={() => {
                    this.context.router.push('/');
                }}>返回首页</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

Component.defaultProps = {
};

export default Component;
