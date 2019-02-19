<template>
      <div class="container">
        <div class="row">
             <div class="col-md-8 col-md-offset-2">
                <div style="margin-bottom: 10px">
                    <el-row>
                        <el-col :span="18">
                        <el-button @click="onCreate">create 1 row</el-button>
                        <el-button @click="onCreate100">create 100 row</el-button>
                        <el-button @click="bulkDelete">bulk delete</el-button>
                        </el-col>

                        <el-col :span="6">
                        <el-input placeholder="search NO." v-model="filters[0].value"></el-input>
                        </el-col>
                    </el-row>
                </div>

                <data-tables :data="data" :action-col="actionCol" :filters="filters" @selection-change="handleSelectionChange">
                <el-table-column type="selection" width="55">
                </el-table-column>

                <el-table-column v-for="title in titles" :prop="title.prop" :label="title.label" :key="title.prop" sortable="custom">
                </el-table-column>
                </data-tables>
             </div>
        </div>
    </div>
</template>

<script>
import Vue from 'vue'
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'
import { DataTables, DataTablesServer } from 'vue-data-tables'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'

Vue.use(ElementUI)
locale.use(lang)
Vue.use(DataTables)
Vue.use(DataTablesServer)
var data, titles

data = [{
    "content": "Water flood",
    "flow_no": "FW201601010001",
    "flow_type": "Repair",
    "flow_type_code": "repair",
    }, {
    "content": "Lock broken",
    "flow_no": "FW201601010002",
    "flow_type": "Repair",
    "flow_type_code": "repair",
    }, {
    "content": "Help to buy some drinks",
    "flow_no": "FW201601010003",
    "flow_type": "Help",
    "flow_type_code": "help"
  }];

  titles = [{
    prop: "flow_no",
    label: "NO."
    }, {
    prop: "content",
    label: "Content"
    }, {
    prop: "flow_type",
    label: "Type"
  }]

// fake server
let serverData = []
for (let i = 0; i < 1000; i++) {
  serverData.push({
    'content': 'Lock broken' + i,
    'flow_no': 'FW20160101000' + i,
    'flow_type': i % 2 === 0 ? 'Repair' : 'Help',
    'flow_type_code': i % 2 === 0 ? 'repair' : 'help',
  })
}

let mockServer = function(res) {
  let datas = serverData.slice()
  let allKeys = Object.keys(data[0])

  // do filter
  res && res.filters && res.filters.forEach(filter => {
    datas = datas.filter(data => {
      let props = (filter.search_prop && [].concat(filter.search_prop)) || allKeys
      return props.some(prop => {
        if (!filter.value || filter.value.length === 0) {
          return true
        }
        return [].concat(filter.value).some(val => {
           return data[prop].toString().toLowerCase().indexOf(val.toLowerCase()) > -1
        })
      })
    })
  })

  // do sort
  if (res.sort && res.sort.order) {
    let order = res.sort.order
    let prop = res.sort.prop
    let isDescending = order === 'descending'

    datas.sort(function(a, b) {
      if (a[prop] > b[prop]) {
        return 1
      } else if (a[prop] < b[prop]) {
        return -1
      } else {
        return 0
      }
    })
    if (isDescending) {
      datas.reverse()
    }
  }

  return {
    data: datas.slice((res.page - 1) * res.pageSize, res.page * res.pageSize),
    req: res,
    ts: new Date(),
    total: datas.length
  }
}

let i = 1

// fake http
function http(res, time = 200) {
  return new Promise((resolve, reject) => {
    setTimeout(_ => {
      var data = mockServer(res)
      console.log('fake server return data: ', data)
      resolve(data)
    }, time)
  })
}


export default {
     data() {
    return {
      data,
      titles,
      filters: [{
        prop: 'flow_no',
        value: ''
      }],
      actionCol: {
        props: {
          label: 'Actionssss',
        },
        buttons: [{
          props: {
            type: 'primary'
          },
          handler: row => {
            this.$message('Edit clicked')
            row.flow_no = 'hello word' + Math.random()
            row.content = Math.random() > 0.5 ? 'Water flood' : 'Lock broken'
            row.flow_type = Math.random() > 0.5 ? 'Repair' : 'Help'
          },
          label: 'Edit'
        }, {
          handler: row => {
            this.data.splice(this.data.indexOf(row), 1)
            this.$message('delete success')
          },
          label: 'delete'
        }]
      },
      selectedRow: []
    }
  },
  methods: {
    onCreate() {
      this.data.push({
        content: "new created",
        flow_no: "FW201601010003" + Math.floor(Math.random() * 100),
        flow_type: "Help",
        flow_type_code: "help"
      })
    },
    onCreate100() {
      [...new Array(100)].map(_ => {
        this.onCreate()
      })
    },
    handleSelectionChange(val) {
      this.selectedRow = val
    },
    bulkDelete() {
      this.selectedRow.map(row => {
        this.data.splice(this.data.indexOf(row), 1)
      })
      this.$message('bulk delete success')
    }
  }
}
</script>