/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_api_key', {
    id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    isactive: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    ipaddr: {
      type: DataTypes.STRING(16),
      allowNull: false,
      unique: true
    },
    apikey: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    updated: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: '0000-00-00 00:00:00'
    }
  }, {
    tableName: 'isost_api_key'
  });
};
