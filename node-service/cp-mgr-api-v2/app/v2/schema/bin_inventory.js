/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('bin_inventory', {
    bin_inv_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    bin_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      unique: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    reserved_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'bin_inventory'
  });
};
