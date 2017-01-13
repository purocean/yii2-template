import Http from '../utils/Http';

export default {
  fetch(resourceName, params, callback) {
    let paramsStr = Object.keys(params).map(key => {
      return key + '=' + encodeURIComponent(params[key]);
    }).join('&');

    return Http.fetch(`/${resourceName}?${paramsStr}`, {}, (data, response) => {
      const pagination = {
        current: parseInt(response.headers.get('X-Pagination-Current-Page')),
        pageSize: parseInt(response.headers.get('X-Pagination-Per-Page')),
        total: parseInt(response.headers.get('X-Pagination-Total-Count')),
      };

      callback(data, pagination);
    });
  },

  fetchStuff(resourceName, callback) {
    return Http.fetch(
      `/${resourceName}/stuff`,
      {},
      data => callback(data)
    );
  },

  fetchOne(resourceName, id, callback) {
    return Http.fetch(
      `/${resourceName}/view?id=${id}`,
      {},
      data => callback(data)
    );
  },

  delete(resourceName, idList, callback) {
    return Http.fetch(
      `/${resourceName}/delete?id=` + idList.toString(),
      {method: 'delete'},
      data => callback(data)
    );
  },

  save(resourceName, body, callback) {
    return Http.fetch(
      `/${resourceName}/save`,
      {
        method: 'post',
        body: body,
      },
      data => callback(data)
    );
  }
}
