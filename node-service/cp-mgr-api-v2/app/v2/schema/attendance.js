/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('attendance', {
    att_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    attn_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_present: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    source: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    reason: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false
    }
  }, {
    tableName: 'attendance'
  });
};
