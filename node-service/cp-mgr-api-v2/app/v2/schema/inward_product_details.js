/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inward_product_details', {
    inward_prd_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    inward_prd_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'inward_products',
        key: 'inward_prd_id'
      }
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_level: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    received_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tot_rec_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mfg_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    exp_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    best_before: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    freshness_per: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    remarks: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
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
    tableName: 'inward_product_details'
  });
};
