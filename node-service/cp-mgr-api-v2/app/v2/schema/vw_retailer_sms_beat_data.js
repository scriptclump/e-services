/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_retailer_sms_beat_data', {
    RETAILERNAME: {
      type: DataTypes.STRING(51),
      allowNull: false,
      defaultValue: ''
    },
    RETAILERMOBILE: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    SALESPERSON: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    SALESPERSONMOBILE: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_retailer_sms_beat_data'
  });
};
