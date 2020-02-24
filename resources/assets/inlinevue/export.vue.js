var template = `
<div>
    <div>
        <div class="row mb-2">
            <div class="col">                        
                <template v-if="downloadStatus.status==2">
                    File download : <a :href="downloadStatus.urlFilename" class="btn btn-sm btn-info" v-text="downloadStatus.filename"></a><br>
                    Tanggal generate : <b v-text="downloadStatus.date"></b><br>
                    Data count : <b v-text="downloadStatus.count"></b>
                </template>  
            </div>
            <div class="col text-right">
                <button @click="requestDownload" class="btn btn-success btn-md d-inline-blcok" :disabled="downloadStatus.status==1">
                    <span class="ion ion-md-cloud-download"></span>&nbsp; Generate Download Terbaru
                </button>
            </div>
        </div>
        <div class="alert alert-success show pr-0" style="max-height: 150px; overflow-x: auto;">
            <template v-if="downloadStatus.status==1 || downloadStatus.status==3">
                <i v-if="downloadStatus.status==1">
                    File download sedang digenerate, mohon tunggu...
                </i>
                <i v-if="downloadStatus.status==3" class="text-danger">
                    Generate gagal !
                </i> - 
                <b>
                    Data count : <span v-text="downloadStatus.count"></span></b> - <b>Processed count : <span v-text="downloadStatus.processedCount"></span>
                </b>                       
                <div v-html="downloadStatus.log" class="p-1" style="background: rgba(0,0,0,0.1); max-height: 110px; overflow-x: auto;"></div>
            </template>
            <template v-else-if="downloadStatus.status==2">
                <i>Log download terkahir :</i>
                <div v-html="downloadStatus.log" class="p-1" style="background: rgba(0,0,0,0.1); max-height: 100px; overflow-x: auto;"></div>
            </template>    
            <template v-else>
                <i><b class="text-danger">-belum ada file download-</b></i>
            </template>                      
        </div>  
    </div>  
</div>
`;
var cExport = Vue.component("c-export", {
    template: template,
    props: [
        "api-export-generate",
        "api-export-status",
        "on-start",
        "on-get-status",
        "on-success",
        "on-fail"
    ],
    $_veeValidate: {
        validator: "new"
    },
    data() {
        return {            
            downloadStatus: {
                status: 0,//status Export, 0 sedang tidak ada proses/selesai, 1 sedang dalam proses, 2 succes, 3 failed
                log: '',//text log
                filename: '',//filename
                urlFilename: '',//url Export file terakhir, setelah generate maka file lama dihapus
                date: ''//filename
            },
        };
    },
    created() {
        this.getDownloadStatus();
    },
    methods: {
        //request generate Export
        requestDownload(){                
            axios.post( this.apiExportGenerate)
                .then((res)=>{
                    this.downloadStatus = res.data.data;
                    this.onStart(this.downloadStatus);
                    showAlert({text: "File stock opname sedang disiapkan untuk didownload, tunggu hingga proses selesai.",type: "info"});
                    this.getDownloadStatus();
                }).catch((res)=>{
                    showAlert({text: "Generate file download gagal : " + res.message,type: "warning"});
                });
        },
        //get status terakhir Export
        getDownloadStatus(){
            var that = this;
            axios.get(this.apiExportStatus , this.loadParams)
                .then((res)=>{
                    var lastStatus = this.downloadStatus.status;
                    this.downloadStatus = res.data.data;
                    this.onGetStatus(this.downloadStatus);
                    //jika belum selesai atau tidak sedang maka request status lagi nanti
                    if(this.downloadStatus.status == 1){
                        setTimeout(function() {
                            that.getDownloadStatus();
                        },1000);
                    //jika berhasil                        
                    }else if(this.downloadStatus.status == 2 && lastStatus == 1){
                        this.onSuccess(this.downloadStatus);
                        showAlert({text: "File stock opname telah selesai dipersiapkan, silahkan didownload.",type: "success"});
                    //jika gagal
                    }else if(this.downloadStatus.status == 3 && lastStatus == 1){
                        this.onFail(this.downloadStatus);
                        showAlert({text: "Generate download gagal, silahkan coba kembali.",type: "warning"});
                    }
                    
                }).catch((res)=>{
                    showAlert({text: "Access status download gagal : " + res.message,type: "danger"});
                });
        }
    }
});