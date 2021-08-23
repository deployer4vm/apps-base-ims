<template>
    <div>
        
        <div class="scanner-button" @click="showScanner">
            <span class="ion ion-ios-barcode"></span> {{button_caption}}
        </div>
        
        <b-modal id="modals-scanner" size="xl" body-class="p-0 m-0" class="p-0 m-0" centered no-fade hide-footer hide-header>
            <div class="scanner-container text-center">
                <div v-show="!isLoading">
                    <video poster="data:image/gif,AAAA" ref="scanner"></video>
                    <div class="overlay-element"></div>
                    <div class="laser"></div>
                </div>
            </div>
            <b-button variant="primary" block @click="closeScanner">Close</b-button>
        </b-modal>
    </div>
</template>

<style scoped>
    video {
        max-width: 100%;
        max-height: 100%;
    }
    .scanner-button {
        cursor: pointer;
    }
    .scanner-container {
        position: relative;
    }
    .overlay-element {
        position: absolute;
        top: 0;
        width: 100%;
        height: 99%;
        background: rgba(30, 30, 30, 0.5);
        -webkit-clip-path: polygon(
            0 0, 0 100%, 10% 100%, 10% 10%, 90% 10%, 90% 90%, 10% 90%, 10% 100%, 100% 100%, 100% 0
        );
        clip-path: polygon(
            0 0, 0 100%, 10% 100%, 10% 10%, 90% 10%, 90% 90%, 10% 90%, 10% 100%, 100% 100%, 100% 0
        );
    }
    .laser {
        width: 80%;
        margin-left: 10%;
        background-color: tomato;
        height: 1px;
        position: absolute;
        top: 10%;
        z-index: 2;
        box-shadow: 0 0 4px red;
        -webkit-animation: scanning 2s infinite;
        animation: scanning 2s infinite;
    }
    @-webkit-keyframes scanning {
        50% {
            -webkit-transform: translateY(200px);
            transform: translateY(200px);
        }
    }
    @keyframes scanning {
        50% {
            -webkit-transform: translateY(200px);
            transform: translateY(200px);
        }
    }
</style>

<script>
//BrowserMultiFormatReader
import { BrowserBarcodeReader} from "node_modules/@zxing/library";
export default {    
    name: "syncomponent-read-barcode",
    props: ['caption'],
    data() {
        return {
            button_caption: 'Barcode',
            isLoading: true,
            codeReader: new BrowserBarcodeReader(),
            isMediaStreamAPISupported:
                navigator &&
                navigator.mediaDevices &&
                "enumerateDevices" in navigator.mediaDevices
        };
    },
    created() {        
        if(this.caption)this.button_caption = this.caption;
    },
    mounted() {
        if (!this.isMediaStreamAPISupported) {
            throw new Exception("Media Stream API is not supported");
            return;
        }
        
    },
    beforeDestroy() {
        this.codeReader.reset();
    },
    methods: {
        showScanner() {
            this.$bvModal.show('modals-scanner');
            var that = this;
            setTimeout(() => {
                that.start();
                that.$refs.scanner.oncanplay = event => {
                    that.isLoading = false;
                    that.$emit("loaded");
                };                
            }, 250);
        },
        closeScanner() {
            this.codeReader.reset();
            this.$bvModal.hide("modals-scanner");
        },
        start() {
            this.codeReader.decodeFromVideoDevice(
                undefined,
                this.$refs.scanner,
                (result, err) => {
                    if (result) {
                        console.log(result.text);
                        this.$emit("scanned", result.text);
                        this.closeScanner();
                    }
                }
            );
        }
    },
}
</script>