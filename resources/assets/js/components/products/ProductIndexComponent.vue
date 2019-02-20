<template>
      <div class="container">
        <div class="row">
          <div class="col-md-4 "> 
              <button class="btn btn-success">
              <router-link :to="{name : 'add-product'}">Add Product</router-link>
              </button>
              <button class="btn btn-danger pull-right">
              <router-link :to="{ name: 'logout' }">Logout</router-link>
              </button>
            </div>
            <div class="col-md-2">
              <button class="btn btn-primary" @click="exportProducts">Export Products</button>
            </div>
            <div class="col-md-6">
              <form v-on:submit.prevent="importProducts">
                <div class="form-group row">
                  <label for="Import" class="col-sm-2 col-form-label">Import</label>
                  <div class="col-sm-8">
                    <input type="file"  :class="{'form-control': true, 'is-danger': errors.has('importExcel') }" class="form-control" name="importExcel" v-validate="'required'" ref="product_file" @change="onFileChange" >
                    <span class="help is-danger" v-if="serverErrors.excel_file">
                        {{ serverErrors.excel_file[0] }}
                    </span>
                  </div>
                  <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary">Import Data</button>
                  </div>
                </div>
              </form>
            </div>
        </div>
        <div class="row">
            
             <div class="col-md-4" v-for="product in list" v-bind:key="product.id">
              
              <div class="card" style="width: 18rem;" >
                <img class="card-img-top" :src="'api/v1/load-image/'+product.product_image" alt="Card image cap">
                <div class="card-body">
                  <h5 class="card-title">{{product.product_name}}</h5>
                  <p class="card-text">
                    <span style="text-decoration: line-through;">{{product.regular_price}}</span>
                    <span>{{product.sale_price}}</span>
                    <div class="pull-right">
                      <router-link :to="{name : 'edit-product',params: { id: product.id }}">
                        Edit
                      </router-link>
                    </div>
                  </p>
                </div>
              </div>
               </div>
               
               
            
        </div>
         <infinite-loading @infinite="infiniteHandler">
                  <div slot="waveDots">Loading...</div>
                  <div slot="no-more">No more message</div>
                  <div slot="no-results">No results message</div>
                </infinite-loading>
    </div>
</template>

<script>
import Vue from 'vue'
import InfiniteLoading from 'vue-infinite-loading';
import VeeValidate from 'vee-validate';
import VueNoty from 'vuejs-noty'
import 'vuejs-noty/dist/vuejs-noty.css'

Vue.use(VueNoty)

const config = {
  aria: true,
  classNames: {},
  classes: true,
  delay: 0,
  dictionary: null,
  errorBagName: 'errors', // change if property conflicts
  events: 'input|blur',
  inject: true,
  locale: 'en',
  validity: false
};
Vue.use(VeeValidate,config);

Vue.use(InfiniteLoading)
export default {
     data() {
    return {
      page: 1,
      list: [],
      excelFile : '',
      // array to hold form errors
      serverErrors: [], 
    }
  },
  methods: {
    /**
     * 
     */
    infiniteHandler($state) {
      axios.get('products', {
        params: {
          page: this.page,
        }
      }).then(({ data }) => {
        if (data.data.length) {
          this.page += 1;
          this.list.push(...data.data);
          $state.loaded();
        } else {
          $state.complete();
        }
      });
    },
  /**
   * 
   */
    onFileChange : function(){
        this.excelFile = this.$refs.product_file.files[0];
    },

    importProducts : function(){
      this.$validator.validateAll().then((result) => {
            if (result) {
                this.formData = new FormData();
                this.formData.append('excel_file', this.excelFile);
                let _this = this;
                axios.post('/products/import',this.formData,{
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }).then(response => {
                                 // clear previous form errors
                                _this.serverErrors =  '';
                                // Success notification
                                this.$noty.success("Your products imported!")
                                this.$router.go()
                    }).catch(function (error) {
                        // handle error
                        // Error message
                        this.$noty.error("Oops, something went wrong!")
                        _this.serverErrors =  error.response.data.error;
                    });
            }
      })
    },

    exportProducts : function(){
      axios({
          url: 'products/export/product',
          method: 'GET',
          headers: { 'Accept': 'application/vnd.ms-excel' },
          responseType: 'blob', // important
        }).then((response) => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'products.xlsx');
          document.body.appendChild(link);
          link.click();
          this.$noty.success("Your products exported!")
        });
    }

  },
  beforeMount() {
      this.list = []
  }
}
</script>
<style>
span.is-danger {
    color: #f22435;
}
.form-control.is-danger{
        border-color: #f22435;
}
</style>
