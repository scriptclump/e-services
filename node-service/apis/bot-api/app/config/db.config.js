module.exports = {
    HOST: "68.183.87.183",
    USER: "fbedevapp",
    PASSWORD: "FbED@v2pP!$",
    DB: "qcebutor",
    dialect: "mysql",
    pool: {
      max: 5,
      min: 0,
      acquire: 30000, // Time in milliseconds
      idle: 10000 // Time in milliseconds
    }
};