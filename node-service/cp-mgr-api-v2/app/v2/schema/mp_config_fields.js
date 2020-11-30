/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mp_config_fields', {
    field_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    field_code: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    field_name: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'mp',
        key: 'mp_id'
      }
    },
    input_type: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    is_required: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    validation: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    field_option: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
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
    tableName: 'mp_config_fields'
  });
};
