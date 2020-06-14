/*
load all application config
*/
let AppConfig = {
    system: require("../../../app/MainApp/config/_system.json"),
    binding: require("../../../app/MainApp/config/_binding.json"),
    client: require("../../../app/MainApp/config/client.json"),
    packageLocal: require("../../../app/MainApp/config/_packageLocal.json"),
    package: require("../../../app/MainApp/config/package.json"),
    listener: require("../../../app/MainApp/config/listener.json"),
    endpoint: require("../../../app/MainApp/config/_endpoint.json"),
    sidenav: require("../../../app/MainApp/config/_sidenav.json"),
    acl: require("../../../app/MainApp/config/_acl.json")
};

AppConfig.isModuleEnable = function(module) {
    return AppConfig.packageLocal[module] && AppConfig.packageLocal[module].enable;
};

AppConfig['sidenavOri'] = JSON.parse(JSON.stringify(AppConfig.sidenav));

// let varPackageLocal = require("../../../app/MainApp/config/packageLocal.json");
// let varPackageLocalEnv = require("../../../app/MainApp/config/packageLocalEnv.json");

// _.forEach(AppConfig.package, (value, index) => {
//     AppConfig.packageLocal[value.package_namespace] = _.merge(
//         _.merge(
//             AppConfig.package[value.package_namespace],
//             varPackageLocal[value.package_namespace]
//         ),
//         varPackageLocalEnv[value.package_namespace]
//     );
// });

export default AppConfig;