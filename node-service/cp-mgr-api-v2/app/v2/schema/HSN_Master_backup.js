/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('HSN_Master_backup', {
    HSNid: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    Chapter: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    ITC_HSCodes: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    HSC_Desc: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    tax_percent: {
      type: DataTypes.FLOAT,
      allowNull: true
    }
  }, {
    tableName: 'HSN_Master_backup'
  });
};
