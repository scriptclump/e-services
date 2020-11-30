/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('spokes', {
    spoke_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    spoke_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'legalentity_warehouses',
        key: 'le_wh_id'
      }
    },
    pincode: {
      type: DataTypes.STRING(6),
      allowNull: true
    }
  }, {
    tableName: 'spokes'
  });
};
