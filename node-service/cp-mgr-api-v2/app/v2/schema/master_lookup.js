/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('master_lookup', {
    master_lookup_id: {
      type: DataTypes.INTEGER(6),
      allowNull: false,
      primaryKey: true
    },
    mas_cat_id: {
      type: DataTypes.INTEGER(6),
      allowNull: true,
      references: {
        model: 'master_lookup_categories',
        key: 'mas_cat_id'
      }
    },
    master_lookup_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    value: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    description: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    image: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    sort_order: {
      type: DataTypes.INTEGER(5),
      allowNull: true
    },
    parent_lookup_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_display: {
      type: DataTypes.INTEGER(1),
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
    tableName: 'master_lookup'
  });
};
