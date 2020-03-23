var template = `
<div>
    <ul v-if="pageCount>=2" class="pagination pagination-sm flex-wrap justify-content-end mb-0">
        <!-- Previous Page Link -->
        <li v-if="currentPage==1" class="page-item disabled" :aria-disabled="true" :aria-label="usedLang.previous">
            <span class="page-link" aria-hidden="true">&lsaquo;</span>
        </li>
        <li v-else class="page-item">
            <a @click="onClick(prevPage)" class="page-link" href="javascript:void(0)" rel="prev" :aria-label="usedLang.previous">&lsaquo;</a>
        </li>

        <!-- Pagination Elements -->
        <template v-for="el in elements">
            <!-- "Three Dots" Separator -->
            <li v-if="el=='...'" class="page-item disabled" :aria-disabled="true"><span class="page-link">{{ el }}</span></li>
            <!-- current page -->
            <li v-else-if="currentPage == el" class="page-item active" aria-current="page"><span class="page-link">{{ el }}</span></li>
            <li v-else class="page-item"><a @click="onClick(el)" class="page-link" href="javascript:void(0)">{{ el }}</a></li>
        </template>

        <!-- Next Page Link -->
        <li class="page-item" v-if="nextPage">
            <a @click="onClick(nextPage)" class="page-link" href="javascript:void(0)" rel="next" :aria-label="usedLang.next">&rsaquo;</a>
        </li>
        <li class="page-item disabled" :aria-disabled="true" :aria-label="usedLang.next">
            <span class="page-link" :aria-hidden="true">&rsaquo;</span>
        </li>
    </ul>
</div>
`;
var cPagination = Vue.component("c-pagination", {
    template: template,
    props: [
        "current-page",
        "page-count",
        "lang",
        "on-page-change"
    ],
    $_veeValidate: {
        validator: "new"
    },
    data() {
        return {    
            usedLang: {
                previous: "Prev",
                next: "Next"
            },
            elements: [1,2,3],
            prevPage:0,
            nextPage:2,
        };
    },
    watch: {
        currentPage(v) {
            this.onPageChange(v);
            this.renderData();
        },
        pageCount(v) {
            this.renderData();
        },
    },
    created() {
        // if(this.currentPage == undefined) this.currentPage = 0;
        // if(this.pageCount == undefined) this.pageCount = 1;
        if(this.lang != undefined){
            if(this.lang.previous != undefined)this.usedLang.previous = this.lang.previous;
            if(this.lang.next != undefined)this.usedLang.next = this.lang.next;
        }
        
        this.renderData();
    },
    mounted: function() {  
        
    },
    methods: {
        onClick(toPage) {
            this.currentPage = toPage;            
        },
        renderData() {
            var tmpEls = [];
            var firstTitik = false;
            var lastTitik = false;
            for(var i=1;i<=this.pageCount;i++)
            {
                if(i>=this.currentPage-4 && i<=this.currentPage+3 ){
                    tmpEls.push(i);
                }else if(i<this.currentPage-4){
                    tmpEls.push(1);
                    tmpEls.push('...');
                    i = this.currentPage-4;
                    firstTitik = true;
                }else if(i>this.currentPage+3){
                    tmpEls.push('...');
                    tmpEls.push(this.pageCount);
                    break;
                }
            }
            this.prevPage = this.currentPage-1;
            this.nextPage = this.currentPage+11;
            if(this.prevPage<=0)this.prevPage=0;
            if(this.nextPage>=this.pageCount)this.nextPage=0;
            this.elements = tmpEls;
        },
        generateLink(page) {
            var linkParam = {
                limit: this.limit,
                offset: this.offset
            };
            return this.url + '?' + params(linkParam) + '&' + params(this.params);
        }
        
    }
});