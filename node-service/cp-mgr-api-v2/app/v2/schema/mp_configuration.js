/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mp_configuration', {
    mp_configuration_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'mp',
        key: 'mp_id'
      }
    },
    key_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    key_value: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    seller_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    se_detail_id: {
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
    tableName: 'mp_configuration'
  });
};
