import React from 'react';
import RichTextEditor from 'react-rte';

class Component extends React.Component {
  static propTypes = {
    onChange: React.PropTypes.func,
  };

  constructor(props){
    super(props);
    this.state = {
       editorValue: RichTextEditor.createEmptyValue(),
    };
    this._currentValue = '';
    this._initialValue = '';
  }

  componentWillMount() {
    this._updateStateFromProps(this.props);
  }

  componentWillReceiveProps(newProps) {
    this._updateStateFromProps(newProps);
  }

  _updateStateFromProps(props) {
    if (this._initialValue !== props.initialValue) {
      this._initialValue = props.initialValue;
      this._onChange(RichTextEditor.createValueFromString(props.initialValue ? props.initialValue : '', props.format))
      this._currentValue = null;
    }
  }

  _onChange(editorValue) {
    this.setState({editorValue});
    this._currentValue = editorValue.toString(this.props.format);
    this.props.onChange(this._currentValue);
  }

  render () {
    return (
      <RichTextEditor
        value={this.state.editorValue}
        onChange={value => this._onChange(value)}
        toolbarConfig={this.props.toolbarConfig}
      />
    );
  }
}

Component.defaultProps = {
  onChange: () => {},
  format: 'html',
  initialValue: null,

  toolbarConfig: {
    // Optionally specify the groups to display (displayed in the order listed).
    display: ['INLINE_STYLE_BUTTONS', 'BLOCK_TYPE_BUTTONS', 'BLOCK_TYPE_DROPDOWN', 'HISTORY_BUTTONS'],
    INLINE_STYLE_BUTTONS: [
      {label: 'Bold', style: 'BOLD', className: ''},
      {label: 'Italic', style: 'ITALIC'},
      {label: 'Underline', style: 'UNDERLINE'},
    ],
    BLOCK_TYPE_DROPDOWN: [
      {label: '正文', style: 'unstyled'},
      {label: '标题 一', style: 'header-one'},
      {label: '标题 二', style: 'header-two'},
      {label: '标题 三', style: 'header-three'},
    ],
    BLOCK_TYPE_BUTTONS: [
      {label: 'UL', style: 'unordered-list-item'},
      {label: 'OL', style: 'ordered-list-item'},
    ],
  }
};


export default Component;
