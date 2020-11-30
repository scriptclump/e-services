/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('replenishment_products', {
    replenishment_product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    product_title: {
      type: DataTypes.STRING(200),
      allowNull: false
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    bin_code: {
      type: DataTypes.STRING(45),
      allowNull: false
    },
    bin_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    rack: {
      type: DataTypes.STRING(45),
      allowNull: false
    },
    replenishment_type: {
      type: DataTypes.ENUM('Priority','Regular'),
      allowNull: true
    },
    replenish_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    placed_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    replenishment_flow: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    replenishment_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    source: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    source_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    status: {
      type: DataTypes.ENUM('Open','Expire','Assigned','Complete'),
      allowNull: false,
      defaultValue: 'Open'
    },
    assigned_user: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    assigned_time: {
      type: DataTypes.DATE,
      allowNull: true
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
    tableName: 'replenishment_products'
  });
};
