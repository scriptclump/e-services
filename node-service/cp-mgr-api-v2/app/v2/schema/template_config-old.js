/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('template_config-old', {
    temp_conf_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    template_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    label: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    read_object_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    read_col_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    read_condition: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    is_required: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    },
    sort_order: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    },
    write_object_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    write_col_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Validation Message: {
      type: DataTypes.STRING(1000),
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
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'template_config-old'
  });
};
