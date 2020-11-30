/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_promotion_slab', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    prmt_det_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: true,
      defaultValue: '0'
    },
    prmt_lock_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    customer_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    end_range: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    price: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    pack_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    start_date: {
      type: DataTypes.STRING(8),
      allowNull: true
    },
    end_date: {
      type: DataTypes.STRING(8),
      allowNull: true
    },
    StateName: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    CustomerType: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    CommonState: {
      type: DataTypes.STRING(1),
      allowNull: false,
      defaultValue: ''
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    wh_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_promotion_slab'
  });
};
