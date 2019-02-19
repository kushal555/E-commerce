<template>
    <span class="help-block">
        <slot></slot>
    </span>
</template>

<script>
    export default {
        props: ['serverErrors']
    }
</script>