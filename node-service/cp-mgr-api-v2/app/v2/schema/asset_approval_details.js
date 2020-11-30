/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('asset_approval_details', {
    asset_approval_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    asset_manfacture_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    asset_brand_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    asset_category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    asset_product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    asset_allocate_to: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    asset_approval_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    asset_comment: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    created_by: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_by: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'asset_approval_details'
  });
};
