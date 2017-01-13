import React from 'react';
import { Link } from 'react-router';
import '../styles/Error.css';

import Png404 from '../images/404.png';
import Png404Msg from '../images/404_msg.png';
import Png404ToIndex from '../images/404_to_index.png';

class Component extends React.Component {
  render() {
    return (
      <div className="err">
        <div id="fulls">
          <div id="container">
            <img className="png" src={Png404} />
            <img className="png msg" src={Png404Msg} />
            <p><Link to="/"><img className="png" src={Png404ToIndex} /></Link></p>
          </div>
        </div>
        <div id="cloud" className="png"></div>
      </div>
    );
  }
}

Component.defaultProps = {
};

export default Component;
