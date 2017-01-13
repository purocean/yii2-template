'use strict';

// Settings configured here will be merged into the final config object.
export default {
  siteName: '站点名字',
  // socketIOPort: 8087,
  socketIOPort: 0, // 不使用 WebSocket
  allowRoutes: ['/', '/error', '/login/*'],
}
