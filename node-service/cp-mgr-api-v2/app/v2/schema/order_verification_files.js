/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('order_verification_files', {
    file_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    container_name: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    file_path: {
      type: DataTypes.STRING(300),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'order_verification_files'
  });
};
