`<template>
    <div>
        <div class="row mb-3">
            <div class="col">                        
                <template v-if="downloadStatus.status==3">
                    File download : <a :href="downloadStatus.fileurl" class="btn btn-sm btn-info" v-text="downloadStatus.filename"></a><br>
                    Tanggal generate : <b v-text="downloadStatus.inputTime"></b><br>
                    Data count : <b v-text="downloadStatus.count"></b>
                </template>  
            </div>
            <div class="col text-right">
                <b-btn @click="requestDownload" :class="config.btnVariant" :disabled="downloadStatus.status==1 || downloadStatus.status==2">
                    <span class="ion ion-md-cloud-download"></span>&nbsp; Generate Download Terbaru
                </b-btn>
            </div>
        </div>
        <div class="alert alert-success show pr-0" style="max-height: 300px; overflow-x: auto;">
            <template v-if="downloadStatus.status==1 || downloadStatus.status==2 || downloadStatus.status==4">
                <i v-if="downloadStatus.status==1 || downloadStatus.status==2" class="p-2 d-inline-block">
                    File download sedang digenerate, mohon tunggu...
                </i>
                <i v-if="downloadStatus.status==4" class="text-danger p-2 d-inline-block">
                    Generate gagal !
                </i> - 
                <b class="p-2 d-inline-block">
                    Data count : <span v-text="downloadStatus.count"></span></b> - <b>Processed count : <span v-text="downloadStatus.processedCount"></span>
                </b>                       
                <div v-if="config.showLog" v-html="downloadStatus.log" class="p-1" style="background: rgba(0,0,0,0.1); max-height: 200px; overflow-x: auto;"></div>
            </template>
            <!-- jika berhasil / selesai -->
            <template v-else-if="downloadStatus.status==3">
                <i class="p-2 d-inline-block">Log download terkahir :</i>
                <div v-if="config.showLog" v-html="downloadStatus.log" class="p-1" style="background: rgba(0,0,0,0.1); max-height: 200px; overflow-x: auto;"></div>
            </template>    
            <!-- jika belum ada data export sebelumnya -->
            <template v-else>
                <i><b class="text-danger">-belum ada file download-</b></i>
            </template>                      
        </div>  
    </div> 
</template>
<script>
export default {
    name: "export",
    props: [
        //url parameter
        "api-export-generate",//url api generate export
        "api-export-status",//url api get status export

        //parameter config tambahan
        "component-config",// config component tambahan, misal style botton atau hide/show log
        "adds-jobs-params", // parameter array tambahan        
        
        //list event callback
        // "on-start",// callback saat export start
        // "on-get-status",// callback setelah get status export berhasil
        // "on-success",// callback setelah proses export selesai dan berhasil
        // "on-fail",// callback setelah proses export gagal
    ],
    data() {
        return {            
            downloadStatus: {
                status: 0,//status Export, 0 sedang tidak ada proses/selesai, 1 sedang dalam proses, 2 succes, 3 failed
                log: '',//text log
                filename: '',//filename
                urlFilename: '',//url Export file terakhir, setelah generate maka file lama dihapus
                date: ''//filename
            },
            config: {
                btnVariant: {
                    'btn-success':true, 
                    'd-inline-block': true
                },
                showLog: true
            }
            
        };
    },
    created() {
        this.getDownloadStatus();
        if(this.componentConfig){
            if(this.componentConfig.btnVariant){
                this.config.btnVariant = this.componentConfig.btnVariant;
            }
            if(this.componentConfig.showLog!=undefined){
                this.config.showLog = this.componentConfig.showLog;
            }
        }
    },
    methods: {
        //request generate Export
        requestDownload(){
            // var param = '';            
            // if(this.addsJobsParams){
            //     param = params(this.addsJobsParams)
            //     if(this.apiExportGenerate.indexOf('?')){
            //         param = '&' + param;
            //     }else{
            //         param = '?' + param;
            //     }
            // }
            this.LocalApi.get(this.apiExportGenerate, {
                    params: this.addsJobsParams
                })
                .then((res)=>{
                    this.downloadStatus = res.data.data;
                    
                    this.$emit('on-start',this.importStatus);

                    this.Web.showAlert({text: "File export sedang disiapkan untuk didownload, tunggu hingga proses selesai.",type: "info"});
                    this.getDownloadStatus();
                }).catch((res)=>{
                    this.Web.showAlert({text: "Generate file download gagal : " + res.message,type: "warning"});
                });
        },
        //get status terakhir Export
        getDownloadStatus(){
            var that = this;
            this.LocalApi.get(this.apiExportStatus)
                .then((res)=>{
                    var lastStatus = this.downloadStatus.status;
                    this.downloadStatus = res.data.data;

                    this.$emit('on-get-status',this.importStatus);

                    //jika belum selesai atau tidak sedang maka request status lagi nanti
                    if(this.downloadStatus.status == 1 || this.downloadStatus.status == 2){
                        setTimeout(function() {
                            that.getDownloadStatus();
                        },1000);
                    //jika berhasil                        
                    }else if(this.downloadStatus.status == 3 && lastStatus == 2){
                        
                        this.$emit('on-success',this.importStatus);

                        this.Web.showAlert({text: "File export telah selesai dipersiapkan, silahkan didownload.",type: "success"});
                        
                    //jika gagal
                    }else if(this.downloadStatus.status == 4 && lastStatus == 2){
                        
                        this.$emit('on-fail',this.importStatus);

                        this.Web.showAlert({text: "Generate download gagal, silahkan coba kembali.",type: "warning"});
                    }
                    
                }).catch((res)=>{
                    this.Web.showAlert({text: "Access status download gagal : " + res.message,type: "danger"});
                });
        }
    }
};
</script>