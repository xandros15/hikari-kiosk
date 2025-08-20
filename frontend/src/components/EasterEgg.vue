<template>
    <div class="easter-egg" :class="subclass">
        <img alt="ester-egg" class="easter-egg-image" v-if="type === 1" src="./../../assets/hikari-egg.png">
        <img alt="ester-egg" class="easter-egg-image" v-else-if="type === 2" src="./../../assets/muji-egg.png">
        <img alt="ester-egg" class="easter-egg-image" v-else-if="type === 3" src="./../../assets/panda-egg.png">
    </div>
</template>

<script>
export default {
    name: "EasterEgg",
    props: {
        type: {
            type: Number,
            required: true,
        }
    },
    computed: {
        subclass() {
            switch (this.type) {
                case 1:
                    return 'easter-egg-top-left'
                case 2:
                    return 'easter-egg-bottom-right'
                case 3:
                    return 'easter-egg-bottom-left'
            }

            return '';
        }
    }
}
</script>

<style scoped lang="scss">
.easter-egg {
    display: block;
    position: fixed;
    height: 500px;
    width: 500px;
    z-index: 2;
    animation: 5s ease 0s backwards;

    &-image {
        height: 100%;
        width: auto;
    }

    &-top-left {
        transform: translate(-100%, -100%);
        animation-name: show-top-left;
        inset: 0 auto auto 0;
    }

    &-bottom-right {
        transform: translate(100%, 100%);
        animation-name: show-bottom-right;
        inset: auto 0 0 auto;
    }

    &-bottom-left {
        transform: translate(-100%, 100%);
        animation-name: show-bottom-left;
        inset: auto auto 0 0;
    }
}

@keyframes show-bottom-right {
    0% {
        transform: translate(100%, 100%);
    }
    50% {
        transform: translate(0);
    }
    100% {
        transform: translate(100%, 100%);
    }
}


@keyframes show-bottom-left {
    0% {
        transform: translate(-100%, 100%);
    }
    50% {
        transform: translate(0);
    }
    100% {
        transform: translate(-100%, 100%);
    }
}


@keyframes show-top-left {
    0% {
        transform: translate(-100%, -100%);
    }
    50% {
        transform: translate(0);
    }
    100% {
        transform: translate(-100%, -100%);
    }
}
</style>
