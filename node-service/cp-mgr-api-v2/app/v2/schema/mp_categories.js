/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mp_categories', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    mp_category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_key: {
      type: DataTypes.STRING(11),
      allowNull: true
    },
    category_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    currency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    charge_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_commission: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    parent_category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_leaf_category: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    level: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    image_url: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    thumbnail_url: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    deleted_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
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
    tableName: 'mp_categories'
  });
};
