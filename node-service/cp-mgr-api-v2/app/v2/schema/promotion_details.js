/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('promotion_details', {
    prmt_det_id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    prmt_tmpl_Id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    prmt_det_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_offer_value: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_offer_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_condition: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_condition_value1: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_condition_value2: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_states: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_customer_group: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_offer_on: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    prmt_det_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_description: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_label: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_free_product: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_free_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    applied_ids: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    offon_free_product: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    is_percent_on_free: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    start_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    end_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    is_repeated: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    prmt_det_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    prmt_lock_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    legal_entity_id: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    prmt_manufacturers: {
      type: DataTypes.STRING(255),
      allowNull: true,
      defaultValue: '0'
    },
    prmt_brands: {
      type: DataTypes.STRING(255),
      allowNull: true,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    product_star: {
      type: DataTypes.STRING(250),
      allowNull: false
    },
    pack_type: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    esu: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    order_type: {
      type: DataTypes.STRING(250),
      allowNull: false,
      defaultValue: '144001'
    },
    warehouse: {
      type: DataTypes.STRING(250),
      allowNull: true
    }
  }, {
    tableName: 'promotion_details'
  });
};
