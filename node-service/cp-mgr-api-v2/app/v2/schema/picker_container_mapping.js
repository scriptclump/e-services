/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('picker_container_mapping', {
    p_id: {
      type: DataTypes.INTEGER(100),
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    productid: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_barcode: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    container_num: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    container_barcode: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    container_type: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    bin_code: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    weight: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    weight_uom: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    picked_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    picked_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    is_verified: {
      type: DataTypes.ENUM('0','1'),
      allowNull: false,
      defaultValue: '0'
    },
    verified_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    verified_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    wrong_picked_reason: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    wrong_picked_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    claimed_at: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'picker_container_mapping'
  });
};
