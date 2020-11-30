/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('filters_sorts', {
    filter_id: {
      type: DataTypes.BIGINT,
      allowNull: false,
      primaryKey: true
    },
    f_name: {
      type: "VARBINARY(255)",
      allowNull: true
    },
    is_filter: {
      type: DataTypes.INTEGER(4),
      allowNull: true,
      defaultValue: '0'
    },
    is_sort: {
      type: DataTypes.INTEGER(4),
      allowNull: true,
      defaultValue: '0'
    },
    key: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    value: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    query: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sorting: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    fs_map_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    active: {
      type: DataTypes.INTEGER(4),
      allowNull: true,
      defaultValue: '0'
    },
    parent: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'filters_sorts'
  });
};
