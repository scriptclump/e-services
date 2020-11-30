/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inbound_product_details', {
    inbound_product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    inbound_qc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'inbound_qc',
        key: 'inbound_qc_id'
      }
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_name: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    image: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    sellable_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    upc: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    receive_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    bad_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    damaged_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    free_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    other_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_size: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ebp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    discount_per: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    mfg_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    expiry_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    sub_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    verified_date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    status: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    qc_comments: {
      type: DataTypes.STRING(300),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'inbound_product_details'
  });
};
