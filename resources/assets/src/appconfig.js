
/*
load all application config
*/
let AppConfig = {
    system: {
        ...require("../../../app/MainApp/config/system.json"),
        ...require("../../../app/MainApp/config/systemEnv.json")
    },
    client: require("../../../app/MainApp/config/client.json"),
    packageLocal: {},
    package: require("../../../app/MainApp/config/package.json"),
    listener: require("../../../app/MainApp/config/listener.json"),
    endpoint: require("../../../app/MainApp/config/endpoint.json")
};

let varPackageLocal = require("../../../app/MainApp/config/packageLocal.json");
let varPackageLocalEnv = require("../../../app/MainApp/config/packageLocalEnv.json");

_.forEach(AppConfig.package, (value, index) => {
    AppConfig.packageLocal[value.package_namespace] = _.merge(
        _.merge(
            AppConfig.package[value.package_namespace],
            varPackageLocal[value.package_namespace]
        ),
        varPackageLocalEnv[value.package_namespace]
    );
});

export default AppConfig;