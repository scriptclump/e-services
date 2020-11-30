/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('crate_history', {
    crate_history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    crate_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    crate_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    picker_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    transaction_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    container_type: {
      type: DataTypes.ENUM('crates','bags','cfc'),
      allowNull: true
    },
    comment: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'crate_history'
  });
};
