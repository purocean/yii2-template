import Alt from '../utils/Alt';
import { message } from 'antd';

import CrudSource from '../sources/CrudSource';

class CrudActions {
  setResourceName(resourceName) {
    return resourceName;
  }

  fetchStuff() {
    return dispatch => {
      dispatch(store => {
        CrudSource.fetchStuff(store.resourceName, (data) => {
          if (data.status === 'ok') {
            this.updateStuff(data.data);
          } else {
            message.error(data.message);
          }
        });
      });
    };
  }

  fetchOne(id) {
    return dispatch => {
      dispatch(store => {
        CrudSource.fetchOne(store.resourceName, id, data => {
          if (data.status === 'ok') {
            this.updateOne(data.data);
          } else {
            message.error(data.message);
          }
        });
      });
    };
  }

  fetch(params = false, appendParams = {}) {
    return dispatch => {
      dispatch(store => {
        if (params === false) {
          params = store.params;
        }

        params = Object.assign(params, appendParams);

        CrudSource.fetch(store.resourceName, params, (list, pagination) => {
          if (list.message) {
            message.error(list.message);
            list = [];
          }

          this.updateData(list, pagination, params);
        });
      });
    };
  }

  save(body) {
    return dispatch => {
      dispatch(store => {
        CrudSource.save(store.resourceName, body, data => {
          if (data.status === 'ok') {
            message.success(data.message);
          } else {
            message.error(data.message);
          }

          this.fetch(false, {page: 1, key: ''});
          this.hideSaveForm();
        });
      });
    };
  }

  delete(idList) {
    return dispatch => {
      dispatch(store => {
        CrudSource.delete(store.resourceName, idList, data => {
          if (data.status === 'ok') {
            message.success(data.message);
          } else {
            message.error(data.message);
          }

          this.fetch();
        });
      });
    };
  }

  showSaveForm(title, initialValues = {}) {
    return { title, initialValues };
  }

  hideSaveForm() {
    return false;
  }

  updateData(list, pagination, params) {
    return { list, pagination, params };
  }

  updateOne(data) {
    return data;
  }

  updateStuff(stuff) {
    return stuff;
  }

  rowSelectionChange(selectedRowKeys, selectedRows) {
    return { selectedRowKeys, selectedRows };
  }

  rowSelectionSelect(record, selected, selectedRows) {
    return { record, selected, selectedRows };
  }

  rowSelectionSelectAll(selected, selectedRows, changeRows) {
    return { selected, selectedRows, changeRows };
  }
}

export default Alt.createActions(CrudActions);
