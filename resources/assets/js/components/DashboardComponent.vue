<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1>Dashboard</h1>

                <p>
                    <router-link :to="{ name: 'logout' }">Logout</router-link>
                </p>

                <p>
                    <router-link :to="{name : 'products'}" >Products</router-link>
                </p>

                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>
                    <div class="panel-body">
                        <p>Data: {{ data }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                data: 'nothing'
            }
        },
        mounted() {
            axios.get('/dashboard')
            .then(response => {
                this.data = response.data.data
            }).catch(error => {

            })
        }
    }
</script>
