<template>
    <div>
    <!--     
    Status Import :

    0 new process
    1 sudah diinput/dispatch ke jobs
    2 process import sudah / sedang berjalan
    3 process import selesai dan berhasil
    4 process import gagal (bisa gagal saat import pertama, ataupun gagal saat approve ataupun cancle approve)
    5 process approve import on progress
    6 process approve import berhasil
    7 process cancel approve import on progress
    8 process cancel approve import berhasil
     -->
        <b-row class="mb-4">
            <b-col md='3' class="text-right mt-2">
                <span class="form-label">Upload File Import</span>
            </b-col>
            <b-col md='9'>
                <b-input-group>
                    <b-file 
                        accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
                        v-model="importFile" 
                        :disabled="!canUpload"
                    ></b-file> 
                    <b-input-group-append v-if="importFile">                              
                        <b-btn variant="danger btn-md md-btn-flat" @click="importFile=null"> 
                            <i class="ion ion-md-trash"></i>
                        </b-btn> 
                    </b-input-group-append>
                </b-input-group>
            </b-col>
        </b-row>

        <!-- <b-form-group :label="'Upload File Import'" label-align-md="right" label-class="pr-md-3" :label-cols-md="3">
            <div>
            </div>
        </b-form-group> -->

        <div class="text-right">                                    
            <b-btn variant="success" @click="uploadImpoart()"
                :disabled="!canUpload"> 
                <span class="ion ion-md-cloud-upload"></span> &nbsp; Upload
            </b-btn> 
        </div>            
        
        <!-- <div v-if="this.apiUrl.formatFile">
            <hr>
            <b-btn variant="info" @click="download(
                this.apiUrl.formatFile,
                apiImportFileFormatFilename?apiImportFileFormatFilename:'Import Format File.xlsx'
            )"> 
                <span class="ion ion-md-cloud-download"></span> &nbsp; Download Format File Import
            </b-btn> 
            <i>Silahkan download format file yang telah disediakan, dan jangan mengubah struktur kolom dan urutan baris data.</i>
        </div> -->        
        
        <!-- import jobs status -->
        <div>

            <hr>
            <div class="row mb-2">
                <div class="col" v-if="importStatus.status==3 || importStatus.status==6 || importStatus.status==8">
                    Tanggal import : <b v-text="importStatus.inputTime"></b><br>
                    Data count : <b v-text="importStatus.processedCount"></b>
                </div>
                <!-- download format file -->
                <div class="col text-right" v-if="isFileFormat && apiUrl.formatFile">                    
                    <b-btn variant="info" @click="download(
                        apiUrl.formatFile,
                        apiImportFileFormatFilename?apiImportFileFormatFilename:'Import Format File.xlsx'
                    )"> 
                        <span class="ion ion-md-cloud-download"></span> &nbsp; Download Format File Import
                    </b-btn> 
                                    
                    <b-btn v-if="apiImportFileFormatInfo" variant="success" @click="showInfoUpload"> 
                        <span class="ion ion-md-information-circle-outline"></span> Panduan
                    </b-btn> 
                    
                    <div><i>Silahkan download format file yang telah disediakan, dan jangan mengubah struktur kolom dan urutan baris data.</i></div>
                </div>
            </div>

            <div class="alert alert-success show pr-0" style="max-height: 300px; overflow-x: auto;">
                <template v-if="importStatus.status==1 || importStatus.status==2 || importStatus.status==4 || importStatus.status==5 || importStatus.status==7">
                    <i v-if="importStatus.status==1 || importStatus.status==2">
                        File import sedang diproses, mohon tunggu...
                    </i>
                    <i v-else-if="importStatus.status==4" class="text-danger">
                        Import gagal !
                    </i>
                    <i v-else-if="importStatus.status==5">
                        Approve on progress
                    </i>
                    <i v-else-if="importStatus.status==7">
                        Cancel import on progress
                    </i> - 
                    <b>
                        Processed count : <span v-text="importStatus.processedCount"></span>
                    </b>
                    <div v-html="importStatus.log" class="p-1" style="background: rgba(0,0,0,0.1); max-height: 200px; overflow-x: auto;"></div>
                </template>
                <!-- jika berhasil -->
                <template v-else-if="importStatus.status==3 || importStatus.status==6 || importStatus.status==8">
                    <i>Log import terakhir :</i>
                    <div v-html="importStatus.log" class="p-1" style="background: rgba(0,0,0,0.1); max-height: 200px; overflow-x: auto;"></div>
                </template>    
                <!-- jika belum ada data import sama sekali sebelumnya -->
                <template v-else>
                    <i><b class="text-danger">-belum ada file import-</b></i>
                </template>                      
            </div>

        </div>

        <!-- jika import selesai dan ada process approval amak tampilkan tombol approve / cancel nya -->
        <div class="m-2 text-right" v-if="importStatus.status==3 && isImportApproval==1">
            <hr>
            <b-btn variant="success" class="m-1" @click="approveImport()">
                <span class="ion ion-ios-checkmark-circle"></span>&nbsp; Approve
            </b-btn> 
            <b-btn variant="danger" class="m-1" @click="cancelImport()">
                <span class="ion ion-md-close"></span>&nbsp; Cancel Import
            </b-btn>

            <div class="m-2">
                <i>Jika data telah sesuai maka silahkan <b>Approve</b> untuk menambahkan data hasil import, jika belum sesuai silahkan <b>Cancel Import</b> dan ulang proses import</i>
            </div>
        </div>

        <b-modal id="modals-import-panduan" :size="'md'" centered no-fade>
            <div slot="modal-title">
                Panduan
            </div>
            <div v-if="apiImportFileFormatInfo" v-html="apiImportFileFormatInfo"></div>
        </b-modal>
    </div>
</template>
<script>
    export default {
        name: "syncomponent-import",
        props: [
            //jika menggunakan struktur default, maka cukup 1 url prefix saja yang dilampirkan   
            //lampirkan tanpa diakhiri /         
            "api-import-mainprefix",

            //url parameter jika api-import-mainprefix tidak diisi
            // lampirkan tanpa diakhiri /   
            "api-import-file-format",//*optional, url format file excel import nya
            "api-import-upload",
            "api-import-status",
            "api-import-approve",
            "api-import-cancel",

            // tambahan parameter jika api-import-file-format diisi
            "api-import-file-format-filename",//*optional, nama file yang didownloadnya
            "api-import-file-format-info",//*optional, text html untuk info tambahan
            
            //parameter config tambahan
            "adds-jobs-params", // parameter array tambahan
            "import-approval",// apakah ada fitur approve & cancel (1/0), default 1 (ada process approval)
            "file-format",// apakah ada fitur download format file import (1/0), default 1 (ada download format file nya)

            //list event callback
            "on-start",
            "on-get-status",
            "on-finish",
            "on-fail",
            "on-approve",
            "on-cancel",
            "on-approve-finish",
            "on-cancel-finish"
        ],
        $_veeValidate: {
            validator: "new"
        },
        data() {
            return { 
                isImportOnProcess: false, //flag untuk detek apakah sedang proses upload ? agar tidak bisa klik berkali-kali           
                // file
                importFile:null,
                importStatus: {
                    status: 0,//status import, 0 sedang tidak ada proses, 1 sudah ada tapi belum start, 2 sedang dalam proses, 3 done, 4 failed
                    log: '',//text log  
                    filename: '',
                    filenamePath: '',
                    count:0,
                    processedCount:0
                },
                apiUrl: {
                    formatFile: '/format-file',
                    upload: '',
                    status: '/status',
                    approve: '/approve',
                    cancel: '/cancel',
                },
                // config
                isImportApproval:1, // apakah import ada fitur approval & cancel nya
                isFileFormat:1,// apakah import ada fitur download format filenya
            };
        },
        computed:{
            canUpload() {
                return this.importStatus.status == undefined || 
                    this.importStatus.status==0 || 
                    this.importStatus.status==4 || 
                    this.importStatus.status==6 || 
                    this.importStatus.status==8 || 
                    (this.isImportApproval==0 && this.importStatus.status==3);
            }
        },
        created() {
            //init apiUrl
            if(this.apiImportMainprefix){
                this.apiUrl = {
                    formatFile: this.apiImportMainprefix + this.apiUrl.formatFile,
                    upload: this.apiImportMainprefix,
                    status: this.apiImportMainprefix + this.apiUrl.status,
                    approve: this.apiImportMainprefix + this.apiUrl.approve,
                    cancel: this.apiImportMainprefix + this.apiUrl.cancel,
                };
            }else{                
                this.apiUrl = {
                    formatFile: this.apiImportFileFormat,
                    upload: this.apiImportUpload,
                    status: this.apiImportStatus,
                    approve: this.apiImportApprove,
                    cancel: this.apiImportCancel,
                };
            }
            if(this.importApproval!=undefined){
                this.isImportApproval = this.importApproval==1?1:0;
            }
            if(this.fileFormat!=undefined){
                this.isFileFormat = this.fileFormat==1?1:0;
            }
            this.getImportStatus(true);    
        },
        methods: {
            showInfoUpload(){
                this.$bvModal.show("modals-import-panduan");
            },
            handleFileUpload(){
                this.importFile = this.$refs.file.files[0];
            },
            uploadImpoart() {
                if(this.isImportOnProcess)return false;
                var that = this;
                var formData = this.Helper.convertToFormData(this.addsJobsParams?this.addsJobsParams:[]);
                formData.append('importFile', this.importFile);
                // formData.append('transactionDate', moment(this.form.transactionDate).format('YYYY-MM-DD'));
                formData.append('importApproval',this.isImportApproval);
                this.isImportOnProcess = true;
                this.LocalApi.post(this.apiUrl.upload, formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'        
                        }
                    }
                )
                .then(res => {
                    this.isImportOnProcess = false;
                    // var lastStatus = this.importStatus.status;
                    this.importStatus = res.data.data;
                    if(this.onStart!=undefined)
                        this.onStart(this.importStatus);
                    this.Web.showAlert({text: "File import berhasil diupload dan sedang diproses, silahkan tunggu hingga proses import selesai",type: "success"});
                    setTimeout(function() {
                        that.getImportStatus();
                    },1000); 
                }).catch((res)=>{
                    this.isImportOnProcess = false;
                    this.Web.showAlert({text: "Upload file import gagal : " + res.message,type: "warning"});
                });
            },
            //get status terakhir import
            getImportStatus(firstLoad=false){
                var that = this;
                this.LocalApi.get(this.apiUrl.status)
                    .then((res)=>{
                        var oldStatus = JSON.parse(JSON.stringify(this.importStatus));
                        this.importStatus = res.data.data;

                        // if (typeof this.onGetStatus === "function")
                        if(this.onGetStatus!=undefined)
                            this.onGetStatus(this.importStatus);
                        
                        //jika belum selesai atau baru mulai upload, maka request status lagi nanti
                        if(this.importStatus.status==1||this.importStatus.status==2||this.importStatus.status==5||this.importStatus.status==7){
                            setTimeout(function() {
                                that.getImportStatus();
                            },1000); 
                        //jika import selesai dan berhasil                          
                        }else if(this.importStatus.status==3 && !firstLoad){
                            if(this.onFinish!=undefined)
                                this.onFinish(this.importStatus);
                            this.Web.showAlert({text: "Proses import selesai.",type: "success"});
                        //jika import selesai dan gagal
                        }else if(this.importStatus.status==4 && !firstLoad){
                            if(this.onFail!=undefined)
                                this.onFail(this.importStatus);
                            this.Web.showAlert({text: "Proses import gagal.",type: "danger"});
                        //jika approve import selesai dan berhasil                          
                        }else if(this.importStatus.status==0 && oldStatus.status == 6 && !firstLoad){
                            if(this.onApproveFinish!=undefined)
                                this.onApproveFinish(this.importStatus);
                            this.importStatus = oldStatus;
                            this.Web.showAlert({text: "Proses Approve selesai.",type: "success"});
                        //jika pembatalan import selesai dan berhasil                          
                        }else if(this.importStatus.status==0 && oldStatus.status == 8 && !firstLoad){
                            if(this.onCancelFinish!=undefined)
                                this.onCancelFinish(this.importStatus);
                            this.importStatus = oldStatus;
                            this.Web.showAlert({text: "Proses pembatalan selesai.",type: "success"});
                        //jika status 0 berarti sudah tidak ada proses
                        }else{
                            this.importFile = null;
                        }                      
                    }).catch((res)=>{
                        this.Web.showAlert({text: "Access status import gagal : " + res.message,type: "warning"});
                    });
            },
            approveImport(){
                var that = this;
                this.LocalApi.post(this.apiUrl.approve)
                    .then((res)=>{
                        this.importStatus = res.data.data;
                        if(this.onApprove!=undefined)
                            this.onApprove(this.importStatus);
                        this.Web.showAlert({text: "Data import diapprove.",type: "success"});
                        setTimeout(function() {
                            that.getImportStatus();
                        },1000);
                    }).catch((res)=>{
                        setTimeout(function() {
                            that.getImportStatus();
                        },1000);
                        this.Web.showAlert({text: "Request approve gagal : " + res.message,type: "warning"});
                    });
            },
            cancelImport(){
                var that = this;
                this.LocalApi.delete(this.apiUrl.cancel)
                    .then((res)=>{
                        this.importStatus = res.data.data;
                        if(this.onCancel!=undefined)
                            this.onCancel(this.importStatus);
                        this.Web.showAlert({text: "Data import dibatalkan.",type: "success"});
                        setTimeout(function() {
                            that.getImportStatus();
                        },1000);
                    }).catch((res)=>{
                        setTimeout(function() {
                            that.getImportStatus();
                        },1000);
                        this.Web.showAlert({text: "Request pembatalan gagal : " + res.message,type: "warning"});
                    });
            }
        }
    };
</script>