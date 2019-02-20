<template>
    <div class="container">
        <div class="row">
        <div class="col-md-2">
            <router-link :to="{name : 'products'}" >Back to Products</router-link>
        </div>
        <div class="col-md-10">
        <div class="form-wrapper">
            <form  v-on:submit.prevent="submitProduct">
                <div class="form-group">
                    <label for="sale_price">Product Name:</label>
                    <input v-validate="'required'" :class="{'form-control': true, 'is-danger': errors.has('product_name') }"  name="product_name" v-model="product.product_name" type="text" placeholder="Enter product name" >
                    <span v-show="errors.has('product_name')" class="help is-danger">{{ errors.first('product_name') }}</span>
                    
                    <span class="help is-danger" v-if="serverErrors.product_name">
                        {{ serverErrors.product_name[0] }}
                    </span>
 
                </div>
                <div class="form-group">
                    <label for="regular_price">Product Regular Price:</label>
                    <input v-validate="'decimal:3|required'" :class="{'form-control': true, 'is-danger': errors.has('regular_price') }" name="regular_price" v-model="product.regular_price" type="text" placeholder="Enter product regular price" >
                    <span v-show="errors.has('regular_price')" class="help is-danger">{{ errors.first('regular_price') }}</span>
                </div>
                <div class="form-group">
                    <label for="sale_price">Product Sale Price:</label>
                    <input v-validate="'decimal:3|required'" name="sale_price" :class="{'form-control': true, 'is-danger': errors.has('sale_price') }"  v-model="product.sale_price" type="text" placeholder="Enter product sale price" >
                    <span v-show="errors.has('sale_price')" class="help is-danger">{{ errors.first('sale_price') }}</span>
                </div>
                <div class="form-group">
                    <label for="product_image">Product Image:</label><br/>
                    <input type="file" ref="product_file" @change="onFileChange" />
                    <img :src="product_image" class="product-preview"  />
                    
                    <span class="help is-danger" v-if="serverErrors.product_image">
                        {{ serverErrors.product_image[0] }}
                    </span>
                </div>
                <div class="form-group">
                    <label>Product Stock</label>
                    <input type="number"  v-validate="'numeric|required'" name="stock" :class="{'form-control': true, 'is-danger': errors.has('stock') }" v-model="product.stock" />
                    <span v-show="errors.has('stock')" class="help is-danger">{{ errors.first('stock') }}</span>
                </div>
                <div class="form-group">
                    <label for="">Product Unit</label>
                    <input type="text" v-validate="'required'" name="product_sku" :class="{'form-control': true, 'is-danger': errors.has('product_sku') }" v-model="product.product_sku" class="form-control" />
                    <span v-show="errors.has('product_sku')" class="help is-danger">{{ errors.first('product_sku') }}</span>
                 </div>
                <button type="submit" class="btn btn-primary">Create Product</button>
            </form>
        </div>
        </div>
        </div>
    </div>
</template>
<script>
import Vue from 'vue'
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

export default {
    data(){
        return {
            product : {

            },
            product_image : '',
             // array to hold form errors
            serverErrors: [], 
        }
    },
    methods : {
          onFileChange : function(){
              this.product.product_image = this.$refs.product_file.files[0];
              this.createImage(this.product.product_image);
          },
        createImage : function(file) {
            var image = new Image();
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.product_image = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        submitProduct : function(){
            this.$validator.validateAll().then((result) => {
            if (result) {

                    this.formData = new FormData();
                    this.formData.append('product_image', this.product.product_image);
                    this.formData.append('product_name', this.product.product_name);
                    this.formData.append('regular_price', this.product.regular_price);
                    this.formData.append('sale_price', this.product.sale_price);
                    this.formData.append('stock', this.product.stock);
                    this.formData.append('product_sku', this.product.product_sku);
                    let _this = this;
                    axios.post('/products',this.formData,{
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }).then(response => {
                                 // clear previous form errors
                                _this.serverErrors =  '';
                                this.$noty.success("Your product created!")
                                _this.$router.push({ name: 'products' })
                    }).catch(function (error) {
                        // handle error
                        this.$noty.error("Oops, something went wrong!")
                        _this.serverErrors =  error.response.data.error;
                    });
                }
            });
        }
    }
}
</script>
<style>
.product-preview{
    min-height:100px;
    min-width:100px;
    max-width: 100px;
    max-height: 100px;
    border: 1px black solid;
}
.form-control.is-danger{
        border-color: #f22435;
}
span.is-danger {
    color: #f22435;
}
</style>
