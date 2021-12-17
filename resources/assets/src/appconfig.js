/*
load all application config
*/
let AppConfig = {
    system: require("../../../app/MainApp/config/_system.json"),
    binding: require("../../../app/MainApp/config/_binding.json"),
    client: require("../../../app/MainApp/config/_client.json"),
    packageLocal: require("../../../app/MainApp/config/_packageLocal.json"),
    packageLocalPerTenant: require("../../../app/MainApp/config/_packageLocalPertenant.json"),
    package: require("../../../app/MainApp/config/package.json"),
    // listener: require("../../../app/MainApp/config/listener.json"),
    endpoint: require("../../../app/MainApp/config/_endpoint.json"),
    sidenav: require("../../../app/MainApp/config/_sidenav.json"),
    acl: require("../../../app/MainApp/config/_acl.json")
};

AppConfig.isModuleEnable = function(module) {
    return AppConfig.packageLocal[module] && AppConfig.packageLocal[module].enable;
};

AppConfig['sidenavOri'] = JSON.parse(JSON.stringify(AppConfig.sidenav));

// jika multi tenant aktif maka replace packageLocal utama dengan packageLocalPerTenant
if(tenantId && AppConfig.system.multitenant.active && AppConfig.packageLocalPerTenant[tenantId]){
    AppConfig.packageLocal = AppConfig.packageLocalPerTenant[tenantId];
}

export default AppConfig;