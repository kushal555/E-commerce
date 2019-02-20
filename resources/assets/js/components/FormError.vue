<template>
    <span class="help-block is-danger">
        <slot></slot>
    </span>
</template>

<script>
    export default {
        props: ['serverErrors']
    }
</script>