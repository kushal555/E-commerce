<template>
      <div class="container">
        <div class="row">
            <div class="col-md-2"> 
              <router-link :to="{name : 'add-product'}">Add Product</router-link>
            </div>
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
               
                <infinite-loading @infinite="infiniteHandler">
                  <div slot="waveDots">Loading...</div>
                  <div slot="no-more">No more message</div>
                  <div slot="no-results">No results message</div>
                </infinite-loading>
            
        </div>
    </div>
</template>

<script>
import Vue from 'vue'
import InfiniteLoading from 'vue-infinite-loading';

Vue.use(InfiniteLoading)
export default {
     data() {
    return {
      page: 1,
      list: [],
    }
  },
  methods: {
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
  }
}
</script>