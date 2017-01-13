import Alt from '../utils/Alt';
import CrudActions from '../actions/CrudActions';

class CrudStore {
  constructor() {
    this.resourceName = '';
    this.status = 'fetchings'; // ok | fetching | getting | deleting | saving
    this.stuff = {};

    this.one = null;
    this.list = [];
    this.pagination = {
      showTotal: total => `总共 ${total} 条`,
      pageSize: 20,
      showQuickJumper: true,
    };

    this.params = {
      page: 1,
      key: '',
    };

    this.saveModal = {
      title: '',
      visible: false,
      initialValues: {},
    };

    this.rowSelectionSelect = {};
    this.rowSelectionSelectAll = {};
    this.selectedRowKeys = [];
    this.selectedRows = [];

    this.bindListeners({
      handleSetResourceName: CrudActions.SET_RESOURCE_NAME,
      handleUpdateStuff: CrudActions.UPDATE_STUFF,
      handleUpdateData: CrudActions.UPDATE_DATA,
      handleUpdateOne: CrudActions.UPDATE_ONE,
      handleFetch: CrudActions.FETCH,
      handleFetchOne: CrudActions.FETCH_ONE,
      handleFetchStuff: CrudActions.FETCH_STUFF,
      handleSave: CrudActions.SAVE,
      handleDelete: CrudActions.DELETE,
      handleShowSaveForm: CrudActions.SHOW_SAVE_FORM,
      handleHideSaveForm: CrudActions.HIDE_SAVE_FORM,
      handleRowSelectionChange: CrudActions.ROW_SELECTION_CHANGE,
      handleRowSelectionSelect: CrudActions.ROW_SELECTION_SELECT,
      handleRowSelectionSelectAll: CrudActions.ROW_SELECTION_SELECT_ALL,
    });
  }

  handleSetResourceName(resourceName) {
    this.list = [];
    this.one = null;
    this.resourceName = resourceName;
  }

  handleUpdateData({ list, pagination, params }) {
    this.list = list;
    this.pagination = pagination;
    this.params = params;

    this.rowSelectionSelect = {};
    this.rowSelectionSelectAll = {};
    this.selectedRowKeys = [];
    this.selectedRows = [];

    this.status = 'ok';
  }

  handleUpdateOne(data) {
    this.one = data;

    this.status = 'ok';
  }

  handleUpdateStuff(stuff) {
    this.stuff = stuff;
  }

  handleFetch(callback) {
    this.status = 'fetching';
    this.list = [];

    callback(this);
  }

  handleFetchOne(callback) {
    this.status = 'getting';
    this.one = null;

    callback(this);
  }

  handleFetchStuff(callback) {
    callback(this);
  }

  handleSave(callback) {
    this.status = 'saving';
    callback(this);
  }

  handleDelete(callback) {
    this.status = 'deleting';
    callback(this);
  }

  handleRowSelectionChange({ selectedRowKeys, selectedRows }) {
    this.selectedRowKeys = selectedRowKeys;
    this.selectedRows = selectedRows;
  }

  handleRowSelectionSelect(params) {
    this.rowSelectionSelect = params;
  }

  handleRowSelectionSelectAll(params) {
    this.rowSelectionSelectAll = params;
  }

  handleShowSaveForm({ title, initialValues }) {
    this.saveModal.title = title,
    this.saveModal.initialValues = initialValues;
    this.saveModal.visible = true;
  }

  handleHideSaveForm() {
    this.saveModal.initialValues = {};
    this.saveModal.visible = false;
  }
}

export default Alt.createStore(CrudStore, 'CrudStore');
