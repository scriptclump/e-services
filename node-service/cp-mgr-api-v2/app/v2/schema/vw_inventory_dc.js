/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_inventory_dc', {
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    dcname: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mrpvalue: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    ptrvalue: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    cpvalue: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    mapvalue: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    espvalue: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    }
  }, {
    tableName: 'vw_inventory_dc'
  });
};
