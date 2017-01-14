Yii2 应用模板，包含企业号同步联系人，扫码登录，使用蚂蚁金服做前端 UI
===============================

特性
-------------------
+ [x] 微信企业号同步联系人
+ [x] 扫码登录
+ [x] [蚂蚁金服](https://ant.design/)前端界面
+ [x] 文件上传处理
+ [x] 微信 jssdk
+ [x] RESTful

安装
-------------------
```bash
# 仅测试 PHP7.0
composer global require "fxp/composer-asset-plugin:^1.2.0"
composer install --prefer-dist
./init
vim /common/config/main.php
vim /common/config/main-local.php
vim /common/config/params-local.php
./yii migrate # 迁移表结构
./yii rbac/reset-password suadmin <password> # 更改超级管理员密码
./yii serve -t=@application/web # 运行开发服务器
cd frontend
npm install # 安装 nodejs 依赖
npm run serve # 运行开发服务器
npm run dist # 前端打包
```

测试
-------------------
```bash
vim /common/config/test-local.php
./yii_test migrate
composer exec codecept build
composer exec codecept run
```

注意
-------------------
默认扫码登录使用 Ajax 轮询方式，使用 WebSocket 方式：
```bash
vim /frontend/src/config/base.js # 配置 WebSocket 端口
cd /frontend/
npm run dist
./yii workerman
```

截图
-------------------
![login_1](./screenshots/login_1.png "账号密码登录")
![login_2](./screenshots/login_2.png "二维码登录")
![login_3](./screenshots/login_3.png "二维码登录")
![user](./screenshots/user.png "用户管理")
