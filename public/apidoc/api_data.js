define({ "api": [  {    "type": "get",    "url": "/city",    "title": "获取城市列表",    "version": "1.0.0",    "name": "city_list",    "group": "Common",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "oid",            "description": "<p>城市唯一id（0）</p>"          },          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "token",            "description": ""          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": " {\n   \"meta\": {\n        \"message\": \"Success\",\n        \"status_code\": 200\n    }\n }\n \"data\":[\n     {\n        \"oid\": 1,\n            \"name\": \"北京\",\n            \"pid\": 0,\n            \"sort\": 1\n      }\n    ]\n}",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/CommonController.php",    "groupTitle": "Common",    "sampleRequest": [      {        "url": "http://saas.me/city"      }    ]  },  {    "type": "post",    "url": "/auth/changePassword",    "title": "修改密码",    "version": "1.0.0",    "name": "user_changePassword",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "password",            "description": ""          },          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "token",            "description": ""          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": "{\n    \"meta\": {\n      \"message\": \"请求成功！\",\n      \"status_code\": 200\n    },\n    \"data\": {\n      \"token\": \"sdfs1sfcd\"\n   }\n  }",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/changePassword"      }    ]  },  {    "type": "post",    "url": "/auth/login",    "title": "用户登录",    "version": "1.0.0",    "name": "user_login",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "account",            "description": "<p>用户账号</p>"          },          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "password",            "description": "<p>设置密码</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": " {\n   \"meta\": {\n     \"message\": \"登录成功！\",\n     \"status_code\": 200\n   }\n }\n \"data\": {\n    \"token\": \"token\"\n  }\n}",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/login"      }    ]  },  {    "type": "post",    "url": "/auth/logout",    "title": "退出登录",    "version": "1.0.0",    "name": "user_logout",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "token",            "description": ""          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": "{\n   \"meta\": {\n     \"message\": \"A token is required\",\n     \"status_code\": 500\n   }\n}",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/logout"      }    ]  },  {    "type": "post",    "url": "/auth/register",    "title": "用户注册",    "version": "1.0.0",    "name": "user_register",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "account",            "description": "<p>用户账号(手机号)</p>"          },          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "password",            "description": "<p>设置密码</p>"          },          {            "group": "Parameter",            "type": "integer",            "optional": false,            "field": "sms_code",            "description": "<p>短信验证码</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": "{\n   \"meta\": {\n     \"message\": \"Success\",\n     \"status_code\": 200\n   }\n }",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/register"      }    ]  },  {    "type": "post",    "url": "/auth/sms",    "title": "获取手机验证码",    "version": "1.0.0",    "name": "user_sms_code",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "integer",            "optional": false,            "field": "phone",            "description": ""          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": "{\n    \"meta\": {\n      \"message\": \"请求成功！\",\n      \"status_code\": 200\n    },\n    \"data\": {\n      \"sms_code\": \"233333\"\n   }\n  }",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/sms"      }    ]  },  {    "type": "post",    "url": "/auth/upToken",    "title": "更新或换取新Token",    "version": "1.0.0",    "name": "user_token",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "token",            "description": ""          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": "{\n    \"meta\": {\n      \"message\": \"更新Token成功！\",\n      \"status_code\": 200\n    },\n    \"data\": {\n      \"token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9\"\n   }\n  }",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/upToken"      }    ]  }] });
