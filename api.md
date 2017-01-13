# 接口
## 所有操作结果
    return {
        status: 'ok'
        message: '操作结果文本'
        ...
    }


## 所有错误
    return {
        status: 'xxx' // status 不为 ok
        message: '错误文本'
        errors:[{...}, ...] // 详细错误
        ...
    }

## 标准 CRUD
### 获取列表
    GET /projects
    return [
        {
            id // 字段
            ...
        }
        {...}
        ...
    ]

### 获取详情
    GET /projects/:id
    return {
        status: 'ok'
        message: '获取成功'
        data: {
            id // 字段
            ...
        }
    }

### 增加编辑
    POST /projects/save
    body {
        id: 0 // 不提供 id 或 id 不为零即为新增
        name: 'name'
        ...
    }

### 删除
    DELETE /projects/:idlist // 如 1,2,3,4


## 项目 | projects | 标准 CRUD
### 字段列表
    'id'                => 'ID',
    'site_id'           => '站点ID',
    'name'              => '项目名称',
    'isp'               => '运营商',
    'construction_type' => '建设方式',
    'comment'           => '备注',
    'overflow_reason'   => '超标原因',
    'benefit_summary'   => '盈亏总结',
    'cost_standard'     => '标准造价',
    'outlay_year'       => '年支出成本',
    'earning_year'      => '年预期收入',
    'static_limit'      => '静态回收期',
    'scores'            => '星级分数',
    'need_leader_check' => '是否需要领导审核',
    'need_yunfa_check'  => '是否需要运发部门审核',
    'status'            => 'Status',
    'created_at'        => 'Created At',
    'updated_at'        => 'Updated At',

### 获取依赖杂项
    GET /projects/stuff
    return {
        isps: [ // 运营商
            '移动': '移动'
            ...
        ]
        constructionTypes: [ // 建设类型
            '新建': '新建'
            '改造': '改造'
        ]
    }

### 等待审核列表
    GET /projects/todolist
    return [
        {...},
        ...
    ]

### 完成审核列表
    GET /projects/donelist
    return [
        {...},
        ...
    ]

### 审核
    POST /projects/audit
    body {
        items: 1,2,3,4 // 操作id
        action: 1 // 1 为同意审批，0为不同意
        reason: '附加原因文本'
    }

### 发起审核
    POST /projects/apply
    params {
        id: 1 // 操作id
    }

## 工作量审批单 | workloads | 标准 CRUD
### 字段定义
    'id'         => 'ID',
    'project_id' => '站点ID',
    'major'      => '专业',
    'summary'    => '摘要',
    'detail'     => '详情',
    'comment'    => '备注',
    'status'     => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',

### 获取依赖杂项
    GET /workloads/stuff
    return {
        majors: [ // 专业列表
            '交流': '交流'
            ...
        ]
    }

### 等待审核列表
    GET /workloads/todolist
    return [
        {...},
        ...
    ]

### 完成审核列表
    GET /workloads/donelist
    return [
        {...},
        ...
    ]

### 审核
    POST /workloads/audit
    body {
        items: 1,2,3,4 // 操作id
        action: 1 // 1 为同意审批，0为不同意
        reason: '附加原因文本'
    }
